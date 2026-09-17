<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmitAssessmentRequest;
use App\Http\Resources\AssessmentAttemptResource;
use App\Http\Resources\QuestionResource;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentAttempt;
use App\Models\Question;
use App\Models\Topic;
use App\Models\UserPoint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class QuestionController extends Controller
{
    public function index(Topic $topic): JsonResponse
    {
        $questions = $topic->questions()
            ->where('type', 'MCQ')
            ->with('options')
            ->inRandomOrder()
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Questions retrieved successfully.',
            'data'    => QuestionResource::collection($questions),
        ]);
    }

    public function submit(SubmitAssessmentRequest $request): JsonResponse
    {
        $validated  = $request->validated();
        $user       = $request->user();
        $hintedIds  = collect($validated['hinted_question_ids'] ?? []);

        $questionIds = collect($validated['answers'])->pluck('question_id');
        $questions   = Question::with('options')->whereIn('id', $questionIds)->get()->keyBy('id');

        $correctOptionMap = $questions->map(
            fn ($q) => $q->options->firstWhere('is_correct', true)?->id
        );

        $score         = 0;
        $earnedPoints  = 0;
        $records       = [];

        foreach ($validated['answers'] as $answer) {
            $questionId       = $answer['question_id'];
            $selectedOptionId = $answer['selected_option_id'];
            $isCorrect        = $correctOptionMap[$questionId] === $selectedOptionId;
            $wasHinted        = $hintedIds->contains($questionId);

            // Hinted questions don't count toward score or earn points
            if ($isCorrect && !$wasHinted) {
                $score++;
                $earnedPoints += 10;
            }

            $records[] = [
                'question_id'        => $questionId,
                'selected_option_id' => $selectedOptionId,
                'is_correct'         => $isCorrect,
            ];
        }

        // Non-hinted questions only — perfect score bonus (+50)
        $nonHintedAnswers = collect($validated['answers'])->filter(
            fn ($a) => !$hintedIds->contains($a['question_id'])
        );
        $allNonHintedCorrect = $nonHintedAnswers->isNotEmpty() &&
            $nonHintedAnswers->every(fn ($a) => $correctOptionMap[$a['question_id']] === $a['selected_option_id']);

        if ($allNonHintedCorrect) {
            $earnedPoints += 50;
        }

        $topicId = $questions->first()?->topic_id;

        $attempt = AssessmentAttempt::create([
            'user_id'         => $user->id,
            'topic_id'        => $topicId,
            'score'           => $score,
            'total_questions' => count($records),
            'started_at'      => now(),
            'submitted_at'    => now(),
        ]);

        $now = now();
        AssessmentAnswer::insert(array_map(fn ($r) => array_merge($r, [
            'attempt_id' => $attempt->id,
            'created_at' => $now,
            'updated_at' => $now,
        ]), $records));

        // Award points
        if ($earnedPoints > 0) {
            $eventType = $allNonHintedCorrect ? 'quiz_perfect' : 'quiz_correct';
            UserPoint::credit(
                $user->id,
                $earnedPoints,
                $eventType,
                "Earned {$earnedPoints} pts from quiz attempt",
                ['attempt_id' => $attempt->id, 'perfect' => $allNonHintedCorrect]
            );
        }

        Cache::forget("dashboard.overview.{$user->id}");
        Cache::forget("progress.overview.{$user->id}");

        $attempt->load(['answers.question.options', 'answers.selectedOption', 'topic']);

        return response()->json([
            'success' => true,
            'message' => 'Assessment submitted successfully.',
            'data'    => array_merge(
                (new AssessmentAttemptResource($attempt))->toArray($request),
                ['points_earned' => $earnedPoints]
            ),
        ], 201);
    }

    public function result(Request $request, AssessmentAttempt $attempt): JsonResponse
    {
        if ($attempt->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to this attempt.',
            ], 403);
        }

        $attempt->load(['answers.question.options', 'answers.selectedOption', 'topic']);

        return response()->json([
            'success' => true,
            'message' => 'Assessment result retrieved successfully.',
            'data'    => new AssessmentAttemptResource($attempt),
        ]);
    }
}
