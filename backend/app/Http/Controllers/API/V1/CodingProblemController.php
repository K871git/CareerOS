<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmitCodingProblemRequest;
use App\Models\CodingProblem;
use App\Models\ProblemSubmission;
use App\Traits\ExecutesCode;
use Illuminate\Http\JsonResponse;

class CodingProblemController extends Controller
{
    use ExecutesCode;

    /* ── List all active problems ──────────────────── */

    public function index(): JsonResponse
    {
        $userId   = auth()->id();
        $problems = CodingProblem::active()
            ->orderBy('order')
            ->orderBy('id')
            ->get(['id', 'title', 'slug', 'difficulty', 'language', 'order']);

        // Attach best submission status per problem for this user
        // $bestStatuses = ProblemSubmission::where('user_id', $userId)
        //     ->whereIn('problem_id', $problems->pluck('id'))
        //     ->selectRaw('problem_id, MAX(CASE WHEN status = "accepted" THEN 2 ELSE 1 END) as rank, MAX(status) as status')
        //     ->groupBy('problem_id')
        //     ->pluck('status', 'problem_id');
        $bestStatuses = ProblemSubmission::where('user_id', $userId)
            ->whereIn('problem_id', $problems->pluck('id'))
            ->selectRaw('problem_id, MAX(CASE WHEN status = "accepted" THEN 1 ELSE 0 END) as is_accepted')
            ->groupBy('problem_id')
            ->pluck('is_accepted', 'problem_id')
            ->map(fn($isAccepted) => $isAccepted ? 'accepted' : 'attempted');

        $data = $problems->map(fn($p) => [
            'id'         => $p->id,
            'title'      => $p->title,
            'slug'       => $p->slug,
            'difficulty' => $p->difficulty,
            'language'   => $p->language,
            'status'     => $bestStatuses[$p->id] ?? null,
        ]);

        return response()->json(['success' => true, 'data' => $data]);
    }

    /* ── Single problem ────────────────────────────── */

    public function show(CodingProblem $codingProblem): JsonResponse
    {
        $visibleCases = $codingProblem->testCases()
            ->where('is_hidden', false)
            ->orderBy('order')
            ->get(['id', 'order', 'label', 'input', 'expected_output']);

        $userId    = auth()->id();
        $lastSubmit = ProblemSubmission::where('user_id', $userId)
            ->where('problem_id', $codingProblem->id)
            ->latest()
            ->first(['status', 'test_cases_passed', 'test_cases_total', 'created_at']);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'           => $codingProblem->id,
                'title'        => $codingProblem->title,
                'slug'         => $codingProblem->slug,
                'difficulty'   => $codingProblem->difficulty,
                'language'     => $codingProblem->language,
                'description'  => $codingProblem->description,
                'constraints'  => $codingProblem->constraints,
                'starter_code' => $codingProblem->starter_code,
                'examples'     => $visibleCases,
                'last_submission' => $lastSubmit,
            ],
        ]);
    }

    /* ── Submit solution ───────────────────────────── */

    public function submit(SubmitCodingProblemRequest $request, CodingProblem $codingProblem): JsonResponse
    {
        $code     = $request->input('code');
        $language = $request->input('language', $codingProblem->language);

        if ($this->isDangerous($language, $code)) {
            return response()->json([
                'success' => false,
                'message' => 'Code contains restricted functions that are not allowed.',
            ], 422);
        }

        $testCases  = $codingProblem->testCases()->orderBy('order')->get();
        $results    = [];
        $totalPassed = 0;
        $startTime  = microtime(true);

        foreach ($testCases as $testCase) {
            [$rawOutput, $exitCode] = $this->executeCode($language, $code, $testCase->input);

            $actual   = rtrim(str_replace("\r\n", "\n", $rawOutput));
            $expected = rtrim(str_replace("\r\n", "\n", $testCase->expected_output));
            $passed   = $actual === $expected && $exitCode === 0;

            if ($passed) {
                $totalPassed++;
                $caseStatus = 'accepted';
            } elseif ($exitCode === 124 || str_contains($rawOutput, '[TLE]')) {
                $caseStatus = 'time_limit_exceeded';
            } elseif ($exitCode !== 0) {
                $caseStatus = 'error';
            } else {
                $caseStatus = 'wrong_answer';
            }

            $result = [
                'id'     => $testCase->id,
                'order'  => $testCase->order,
                'label'  => $testCase->label ?? ('Test Case ' . $testCase->order),
                'passed' => $passed,
                'hidden' => $testCase->is_hidden,
                'status' => $caseStatus,
            ];

            // Only expose input/expected/actual for non-hidden test cases
            if (!$testCase->is_hidden) {
                $result['input']    = $testCase->input;
                $result['expected'] = $testCase->expected_output;
                $result['actual']   = $actual;
            }

            $results[] = $result;
        }

        $elapsedMs   = (int) round((microtime(true) - $startTime) * 1000);
        $totalCases  = count($testCases);
        $allPassed   = $totalPassed === $totalCases;

        // Determine overall status — TLE/error takes priority over wrong answer
        $overallStatus = 'wrong_answer';
        if ($allPassed) {
            $overallStatus = 'accepted';
        } else {
            foreach ($results as $r) {
                if ($r['status'] === 'time_limit_exceeded') {
                    $overallStatus = 'time_limit_exceeded';
                    break;
                }
                if ($r['status'] === 'error') {
                    $overallStatus = 'error';
                    break;
                }
            }
        }

        ProblemSubmission::create([
            'user_id'           => auth()->id(),
            'problem_id'        => $codingProblem->id,
            'language'          => $language,
            'code'              => $code,
            'status'            => $overallStatus,
            'test_cases_passed' => $totalPassed,
            'test_cases_total'  => $totalCases,
            'execution_time_ms' => $elapsedMs,
        ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'status'            => $overallStatus,
                'test_cases_passed' => $totalPassed,
                'test_cases_total'  => $totalCases,
                'execution_time_ms' => $elapsedMs,
                'results'           => $results,
            ],
        ]);
    }

    /* ── User's submission history ─────────────────── */

    public function submissions(CodingProblem $codingProblem): JsonResponse
    {
        $submissions = ProblemSubmission::where('user_id', auth()->id())
            ->where('problem_id', $codingProblem->id)
            ->latest()
            ->limit(10)
            ->get(['id', 'language', 'status', 'test_cases_passed', 'test_cases_total', 'execution_time_ms', 'created_at']);

        return response()->json(['success' => true, 'data' => $submissions]);
    }
}
