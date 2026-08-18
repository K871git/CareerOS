<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\TheoryCompletion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TheoryLevelController extends Controller
{
    // Score needed to pass each level (out of 10)
    // Level 1: 75% → 8/10, Level 2: 85% → 9/10, Level 3: 95% → 10/10
    const PASS_THRESHOLDS  = [1 => 8, 2 => 9, 3 => 10];
    const PASS_PERCENTAGES = [1 => 75, 2 => 85, 3 => 95];

    const AREAS = [
        'languages'         => 'Languages',
        'frameworks'        => 'Frameworks',
        'networking'        => 'Networking',
        'operating-systems' => 'Operating Systems',
        'databases'         => 'Databases',
        'system-design'     => 'System Design',
        'sdlc'              => 'SDLC',
        'data-structures'   => 'Data Structures',
    ];

    const ACTIVE_AREAS = ['languages'];

    public function areas(Request $request): JsonResponse
    {
        $completions = TheoryCompletion::where('user_id', $request->user()->id)->get();

        $areas = collect(self::AREAS)->map(function (string $label, string $slug) use ($completions) {
            $areaCompletions = $completions->where('theory_area', $slug);

            return [
                'slug'             => $slug,
                'title'            => $label,
                'available'        => in_array($slug, self::ACTIVE_AREAS),
                'levels_completed' => $areaCompletions->where('passed', true)->count(),
                'total_levels'     => 3,
            ];
        })->values();

        return response()->json(['data' => $areas]);
    }

    public function levels(Request $request, string $area): JsonResponse
    {
        if (!array_key_exists($area, self::AREAS)) {
            return response()->json(['message' => 'Area not found.'], 404);
        }

        $completions = TheoryCompletion::where('user_id', $request->user()->id)
            ->where('theory_area', $area)
            ->get()
            ->keyBy('level');

        $levels = collect([1, 2, 3])->map(function (int $lvl) use ($completions) {
            $completion = $completions->get($lvl);
            $prevPassed = $lvl === 1 ? true : (bool) ($completions->get($lvl - 1)?->passed ?? false);

            return [
                'level'           => $lvl,
                'locked'          => !$prevPassed,
                'completed'       => (bool) ($completion?->passed ?? false),
                'score'           => $completion?->score,
                'pass_threshold'  => self::PASS_THRESHOLDS[$lvl],
                'pass_percentage' => self::PASS_PERCENTAGES[$lvl],
            ];
        });

        return response()->json(['data' => $levels]);
    }

    public function examQuestions(Request $request, string $area, int $level): JsonResponse
    {
        if (!array_key_exists($area, self::AREAS)) {
            return response()->json(['message' => 'Area not found.'], 404);
        }

        if ($level < 1 || $level > 3) {
            return response()->json(['message' => 'Invalid level.'], 422);
        }

        if ($level > 1) {
            $prevPassed = TheoryCompletion::where('user_id', $request->user()->id)
                ->where('theory_area', $area)
                ->where('level', $level - 1)
                ->where('passed', true)
                ->exists();

            if (!$prevPassed) {
                return response()->json(['message' => 'Complete the previous level first.'], 403);
            }
        }

        $questions = Question::where('theory_area', $area)
            ->where('theory_level', $level)
            ->where('type', 'MCQ')
            ->with('options')
            ->inRandomOrder()
            ->limit(10)
            ->get();

        if ($questions->count() < 10) {
            return response()->json(['message' => 'Not enough questions available for this level yet.'], 422);
        }

        $questions->each(function ($question) {
            $question->options->each(fn ($opt) => $opt->makeHidden('is_correct'));
        });

        return response()->json(['data' => $questions]);
    }

    public function submitExam(Request $request, string $area, int $level): JsonResponse
    {
        if (!array_key_exists($area, self::AREAS)) {
            return response()->json(['message' => 'Area not found.'], 404);
        }

        if ($level < 1 || $level > 3) {
            return response()->json(['message' => 'Invalid level.'], 422);
        }

        $validated = $request->validate([
            'answers'   => ['required', 'array', 'size:10'],
            'answers.*' => ['required', 'integer'],
        ]);

        $questionIds = array_map('intval', array_keys($validated['answers']));
        $questions   = Question::whereIn('id', $questionIds)
            ->with('options')
            ->get()
            ->keyBy('id');

        $score = 0;
        foreach ($validated['answers'] as $questionId => $optionId) {
            $question = $questions->get((int) $questionId);
            if ($question) {
                $correct = $question->options->firstWhere('is_correct', true);
                if ($correct && $correct->id === (int) $optionId) {
                    $score++;
                }
            }
        }

        $threshold = self::PASS_THRESHOLDS[$level] ?? 10;
        $passed    = $score >= $threshold;

        TheoryCompletion::updateOrCreate(
            ['user_id' => $request->user()->id, 'theory_area' => $area, 'level' => $level],
            ['score' => $score, 'passed' => $passed],
        );

        return response()->json([
            'data' => [
                'score'      => $score,
                'total'      => 10,
                'passed'     => $passed,
                'threshold'  => $threshold,
                'percentage' => self::PASS_PERCENTAGES[$level],
            ],
        ]);
    }
}
