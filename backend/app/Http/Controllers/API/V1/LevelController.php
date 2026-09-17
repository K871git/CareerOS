<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\LevelCompletion;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\UserPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LevelController extends Controller
{
    // GET /v1/subjects/by-slug/{slug}
    public function bySlug(string $slug)
    {
        $subject = Subject::where('slug', $slug)->firstOrFail();

        return response()->json([
            'success' => true,
            'message' => 'Subject found.',
            'data'    => $subject,
        ]);
    }

    // GET /v1/subjects/{subject}/levels
    public function index(Subject $subject)
    {
        $userId = auth()->id();

        $levels = Cache::remember("level.status.{$userId}.{$subject->id}", 300, function () use ($userId, $subject) {
            $completions = LevelCompletion::where('user_id', $userId)
                ->where('subject_id', $subject->id)
                ->get()
                ->keyBy('level');

            $levels = [];
            for ($level = 1; $level <= 5; $level++) {
                $completion = $completions->get($level);
                $locked     = $level > 1 && ! ($completions->get($level - 1)?->passed ?? false);

                $levels[] = [
                    'level'     => $level,
                    'locked'    => $locked,
                    'completed' => $completion?->passed ?? false,
                    'score'     => $completion?->score,
                ];
            }

            return $levels;
        });

        return response()->json([
            'success' => true,
            'message' => 'Level status retrieved.',
            'data'    => $levels,
        ]);
    }

    // GET /v1/subjects/{subject}/levels/{level}/topics
    public function topics(Subject $subject, int $level)
    {
        $topics = Topic::where('subject_id', $subject->id)
            ->where('level', $level)
            ->orderBy('display_order')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Topics retrieved.',
            'data'    => $topics,
        ]);
    }

    // GET /v1/subjects/{subject}/levels/{level}/exam
    public function examQuestions(Subject $subject, int $level)
    {
        if ($level > 1) {
            $prevPassed = LevelCompletion::where('user_id', auth()->id())
                ->where('subject_id', $subject->id)
                ->where('level', $level - 1)
                ->where('passed', true)
                ->exists();

            if (! $prevPassed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Complete the previous level first.',
                ], 403);
            }
        }

        $topicIds = Topic::where('subject_id', $subject->id)
            ->where('level', $level)
            ->pluck('id');

        $questions = Question::whereIn('topic_id', $topicIds)
            ->where('type', 'MCQ')
            ->with('options')
            ->inRandomOrder()
            ->limit(10)
            ->get()
            ->map(fn ($q) => [
                'id'         => $q->id,
                'question'   => $q->question,
                'difficulty' => $q->difficulty,
                'options'    => $q->options->shuffle()->map(fn ($o) => [
                    'id'          => $o->id,
                    'option_text' => $o->option_text,
                ]),
            ]);

        if ($questions->count() < 10) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough questions available for this level exam.',
                'data'    => [],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Exam questions ready.',
            'data'    => $questions,
        ]);
    }

    // POST /v1/subjects/{subject}/levels/{level}/exam
    public function submitExam(Request $request, Subject $subject, int $level)
    {
        $request->validate([
            'answers'   => 'required|array|min:10|max:10',
            'answers.*' => 'required|integer',
        ]);

        $topics       = Topic::where('subject_id', $subject->id)->where('level', $level)->get();
        $topicIds     = $topics->pluck('id');
        $topicTitleMap = $topics->pluck('title', 'id');

        $questionIds = array_keys($request->answers);

        $questions = Question::whereIn('id', $questionIds)
            ->whereIn('topic_id', $topicIds)
            ->with('options')
            ->get()
            ->keyBy('id');

        $score         = 0;
        $answerDetails = [];

        foreach ($request->answers as $questionId => $selectedOptionId) {
            $question = $questions->get($questionId);
            if (! $question) {
                continue;
            }
            $correctOption  = $question->options->firstWhere('is_correct', true);
            $selectedOption = $question->options->firstWhere('id', $selectedOptionId);
            $isCorrect      = $correctOption && $correctOption->id == $selectedOptionId;

            if ($isCorrect) {
                $score++;
            }

            $answerDetails[] = [
                'question_id'     => $question->id,
                'question'        => $question->question,
                'topic_id'        => $question->topic_id,
                'topic_title'     => $topicTitleMap->get($question->topic_id, 'General'),
                'difficulty'      => $question->difficulty,
                'is_correct'      => $isCorrect,
                'selected_option' => $selectedOption?->option_text ?? 'Not answered',
                'correct_option'  => $correctOption?->option_text ?? '',
            ];
        }

        $passed = $score >= 8;
        $userId = auth()->id();

        LevelCompletion::updateOrCreate(
            ['user_id' => $userId, 'subject_id' => $subject->id, 'level' => $level],
            ['score' => $score, 'passed' => $passed]
        );

        // Award points — +10 per correct, +150 pass bonus
        $pointsEarned = 0;
        if ($score > 0) {
            $pointsEarned += $score * 10;
            UserPoint::credit($userId, $score * 10, 'quiz_correct', "Level {$level} exam — {$score} correct answers", [
                'subject_id' => $subject->id,
                'level'      => $level,
                'score'      => $score,
            ]);
        }
        if ($passed) {
            $pointsEarned += 150;
            UserPoint::credit($userId, 150, 'quiz_perfect', "Level {$level} exam passed — completion bonus", [
                'subject_id' => $subject->id,
                'level'      => $level,
            ]);
        }

        Cache::forget("level.status.{$userId}.{$subject->id}");
        Cache::forget("dashboard.overview.{$userId}");
        Cache::forget("progress.overview.{$userId}");

        return response()->json([
            'success' => true,
            'message' => $passed ? 'Congratulations! Level completed.' : 'Keep practicing.',
            'data'    => [
                'score'         => $score,
                'total'         => 10,
                'passed'        => $passed,
                'points_earned' => $pointsEarned,
                'answers'       => $answerDetails,
            ],
        ]);
    }
}
