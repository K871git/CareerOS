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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $validated = $request->validated();
        $user      = $request->user();

        $questionIds = collect($validated['answers'])->pluck('question_id');
        $questions   = Question::with('options')->whereIn('id', $questionIds)->get()->keyBy('id');

        $correctOptionMap = $questions->map(
            fn ($q) => $q->options->firstWhere('is_correct', true)?->id
        );

        $score   = 0;
        $records = [];

        foreach ($validated['answers'] as $answer) {
            $questionId       = $answer['question_id'];
            $selectedOptionId = $answer['selected_option_id'];
            $isCorrect        = $correctOptionMap[$questionId] === $selectedOptionId;

            if ($isCorrect) {
                $score++;
            }

            $records[] = [
                'question_id'        => $questionId,
                'selected_option_id' => $selectedOptionId,
                'is_correct'         => $isCorrect,
            ];
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

        foreach ($records as $record) {
            AssessmentAnswer::create(array_merge($record, ['attempt_id' => $attempt->id]));
        }

        $attempt->load(['answers.question.options', 'answers.selectedOption', 'topic']);

        return response()->json([
            'success' => true,
            'message' => 'Assessment submitted successfully.',
            'data'    => new AssessmentAttemptResource($attempt),
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
