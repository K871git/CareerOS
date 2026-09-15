<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\TheoryCompletion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TheoryLevelController extends Controller
{
    // Score needed to pass each level (out of 10)
    // Level 1: 75% → 8/10, Level 2: 85% → 9/10, Level 3: 95% → 10/10
    const PASS_THRESHOLDS  = [1 => 8, 2 => 9, 3 => 10];
    const PASS_PERCENTAGES = [1 => 75, 2 => 85, 3 => 95];

    public function areas(Request $request): JsonResponse
    {
        $areas       = config('theory.areas');
        $activeAreas = config('theory.active_areas');
        $completions = TheoryCompletion::where('user_id', $request->user()->id)->get();

        $areas = collect($areas)->map(function (string $label, string $slug) use ($completions, $activeAreas) {
            $areaCompletions = $completions->where('theory_area', $slug);

            return [
                'slug'             => $slug,
                'title'            => $label,
                'available'        => in_array($slug, $activeAreas),
                'levels_completed' => $areaCompletions->where('passed', true)->count(),
                'total_levels'     => 3,
            ];
        })->values();

        return response()->json(['data' => $areas]);
    }

    public function levels(Request $request, string $area): JsonResponse
    {
        if (!array_key_exists($area, config('theory.areas'))) {
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
        if (!array_key_exists($area, config('theory.areas'))) {
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

        return response()->json([
            'data' => $questions->map(fn ($q) => [
                'id'         => $q->id,
                'type'       => $q->type,
                'difficulty' => $q->difficulty,
                'question'   => $q->question,
                'options'    => $q->options->shuffle()->map(fn ($o) => [
                    'id'          => $o->id,
                    'option_text' => $o->option_text,
                ]),
            ]),
        ]);
    }

    public function submitExam(Request $request, string $area, int $level): JsonResponse
    {
        if (!array_key_exists($area, config('theory.areas'))) {
            return response()->json(['message' => 'Area not found.'], 404);
        }

        if ($level < 1 || $level > 3) {
            return response()->json(['message' => 'Invalid level.'], 422);
        }

        $validated = $request->validate([
            'answers'   => ['required', 'array', 'size:10'],
            'answers.*' => ['required', 'integer'],
        ]);

        // Only score questions that actually belong to this area+level (prevents answer-stuffing)
        $questionIds = array_map('intval', array_keys($validated['answers']));
        $questions   = Question::whereIn('id', $questionIds)
            ->where('theory_area', $area)
            ->where('theory_level', $level)
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

        $userId = $request->user()->id;

        TheoryCompletion::updateOrCreate(
            ['user_id' => $userId, 'theory_area' => $area, 'level' => $level],
            ['score' => $score, 'passed' => $passed],
        );

        Cache::forget("progress.overview.{$userId}");
        Cache::forget("dashboard.overview.{$userId}");

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
