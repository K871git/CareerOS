<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\LevelCompletion;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Http\Request;

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
        $completions = LevelCompletion::where('user_id', auth()->id())
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
                'options'    => $q->options->map(fn ($o) => [
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

        $topicIds    = Topic::where('subject_id', $subject->id)
            ->where('level', $level)
            ->pluck('id');
        $questionIds = array_keys($request->answers);

        $questions = Question::whereIn('id', $questionIds)
            ->whereIn('topic_id', $topicIds)
            ->with('options')
            ->get()
            ->keyBy('id');

        $score = 0;
        foreach ($request->answers as $questionId => $selectedOptionId) {
            $question = $questions->get($questionId);
            if (! $question) {
                continue;
            }
            $correct = $question->options->firstWhere('is_correct', true);
            if ($correct && $correct->id == $selectedOptionId) {
                $score++;
            }
        }

        $passed = $score === 10;

        LevelCompletion::updateOrCreate(
            [
                'user_id'    => auth()->id(),
                'subject_id' => $subject->id,
                'level'      => $level,
            ],
            [
                'score'  => $score,
                'passed' => $passed,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => $passed ? 'Congratulations! Level completed.' : 'Keep practicing.',
            'data'    => [
                'score'  => $score,
                'total'  => 10,
                'passed' => $passed,
            ],
        ]);
    }
}
