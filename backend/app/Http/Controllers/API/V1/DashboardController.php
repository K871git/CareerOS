<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AssessmentAttempt;
use App\Models\Lesson;
use App\Models\LevelCompletion;
use App\Models\UserProgress;
use App\Models\UserProfile;
use App\Models\UserSkill;
use App\Services\UserAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    private const SKILL_LABELS = [
        0 => 'Not Started',
        1 => 'Beginner',
        2 => 'Developing',
        3 => 'Proficient',
        4 => 'Advanced',
    ];

    public function __construct(private UserAnalyticsService $analytics) {}

    public function overview(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $data = Cache::remember("dashboard.overview.{$userId}", 120, function () use ($userId) {
            // --- Lesson progress ---
            $totalLessons     = Lesson::count();
            $completedLessons = UserProgress::where('user_id', $userId)
                ->where('status', 'COMPLETED')
                ->count();

            // --- Quiz stats aggregated in SQL (no full-table PHP load) ---
            $quiz = $this->analytics->quizBySubject($userId);
            $quizzesTaken  = $quiz['quizzesTaken'];
            $totalAnswered = $quiz['totalAnswered'];
            $totalCorrect  = $quiz['totalCorrect'];
            $accuracy      = $quiz['accuracy'];
            $quizBySubject = $quiz['bySubject']; // already a plain array from the service

            // avg quiz score = average of per-attempt percentages (different from accuracy)
            $avgQuizScore = $quizzesTaken > 0
                ? round(
                    AssessmentAttempt::where('user_id', $userId)
                        ->selectRaw('AVG(CASE WHEN total_questions > 0 THEN score / total_questions * 100 ELSE 0 END) AS avg')
                        ->value('avg') ?? 0,
                    1
                )
                : 0.0;

            // --- Learning level completions ---
            $learningLevelsPassed = LevelCompletion::where('user_id', $userId)
                ->where('passed', true)
                ->count();

            // --- Skill level (based on quiz accuracy) ---
            $skillLevel = match (true) {
                $quizzesTaken === 0 => 0,
                $accuracy < 40     => 1,
                $accuracy < 60     => 2,
                $accuracy < 75     => 3,
                default            => 4,
            };
            $skillLabel = self::SKILL_LABELS[$skillLevel];

            // --- User profile ---
            $profile     = UserProfile::where('user_id', $userId)->first();
            $profileData = $profile ? [
                'target_role'      => $profile->target_role,
                'career_goal'      => $profile->career_goal,
                'experience_level' => $profile->experience_level,
            ] : null;

            // --- User skills from career assessment ---
            $userSkills = UserSkill::where('user_id', $userId)
                ->with('skill')
                ->get()
                ->map(fn ($us) => [
                    'name'     => $us->skill->name,
                    'category' => $us->skill->category,
                    'level'    => $us->level,
                    'score'    => $us->score,
                ])
                ->values()
                ->all();

            // --- Weak areas: topics scoring below 70% (SQL-aggregated) ---
            $weakAreas = collect($this->analytics->quizByTopic($userId))
                ->filter(fn ($d) => $d['avg_score'] < 70)
                ->sortBy('avg_score')
                ->values()
                ->take(5);

            // --- Recommendations ---
            $recommendations = [];

            if ($quizzesTaken === 0) {
                $recommendations[] = [
                    'type'          => 'get_started',
                    'title'         => 'Take your first quiz',
                    'description'   => 'Head to Practice to start answering questions and tracking progress.',
                    'topic_id'      => null,
                    'topic_slug'    => null,
                    'subject_title' => null,
                    'route'         => '/practice',
                ];
            } else {
                foreach ($weakAreas->take(3) as $area) {
                    $recommendations[] = [
                        'type'          => 'weak_topic',
                        'title'         => 'Revisit: ' . $area['topic_title'],
                        'description'   => 'Your score is ' . $area['avg_score'] . '% — retry this topic to improve.',
                        'topic_id'      => $area['topic_id'],
                        'topic_slug'    => $area['topic_slug'],
                        'subject_title' => $area['subject_title'],
                        'route'         => '/practice/topics/' . $area['topic_id'],
                    ];
                }
            }

            if ($completedLessons === 0) {
                $recommendations[] = [
                    'type'          => 'start_learning',
                    'title'         => 'Complete your first lesson',
                    'description'   => 'Structured learning builds the foundation for better quiz scores.',
                    'topic_id'      => null,
                    'topic_slug'    => null,
                    'subject_title' => null,
                    'route'         => '/learning',
                ];
            }

            if (empty($recommendations)) {
                $recommendations[] = [
                    'type'          => 'explore',
                    'title'         => 'All topics above 70% — great work!',
                    'description'   => 'Try harder levels or explore a new subject to keep improving.',
                    'topic_id'      => null,
                    'topic_slug'    => null,
                    'subject_title' => null,
                    'route'         => '/practice',
                ];
            }

            // --- Recent quiz attempts (last 5) ---
            $recentAttempts = AssessmentAttempt::where('user_id', $userId)
                ->with(['topic.subject'])
                ->orderByDesc('submitted_at')
                ->limit(5)
                ->get()
                ->map(fn ($attempt) => [
                    'attempt_id'      => $attempt->id,
                    'topic_id'        => $attempt->topic_id,
                    'topic_title'     => $attempt->topic?->title ?? 'Unknown',
                    'subject_title'   => $attempt->topic?->subject?->title ?? 'Unknown',
                    'score'           => $attempt->score,
                    'total_questions' => $attempt->total_questions,
                    'percentage'      => $attempt->total_questions > 0
                        ? round($attempt->score / $attempt->total_questions * 100, 1)
                        : 0.0,
                    'submitted_at'    => $attempt->submitted_at?->toISOString(),
                ]);

            // --- Recent lesson activity (last 5) ---
            $recentActivity = UserProgress::where('user_id', $userId)
                ->where('status', 'COMPLETED')
                ->with(['lesson.topic.subject'])
                ->orderByDesc('completed_at')
                ->limit(5)
                ->get()
                ->map(fn ($p) => [
                    'type'         => 'lesson_completed',
                    'description'  => 'Completed: ' . ($p->lesson?->title ?? 'Lesson'),
                    'subject_name' => $p->lesson?->topic?->subject?->title,
                    'created_at'   => $p->completed_at?->toISOString() ?? $p->updated_at->toISOString(),
                ]);

            return [
                'summary' => [
                    'lessons_completed'        => $completedLessons,
                    'lessons_total'            => $totalLessons,
                    'lessons_percentage'       => $totalLessons > 0
                        ? round($completedLessons / $totalLessons * 100, 1)
                        : 0.0,
                    'quizzes_taken'            => $quizzesTaken,
                    'total_questions_answered' => $totalAnswered,
                    'total_correct'            => $totalCorrect,
                    'accuracy'                 => $accuracy,
                    'avg_quiz_score'           => $avgQuizScore,
                    'learning_levels_passed'   => $learningLevelsPassed,
                    'skill_level'              => $skillLevel,
                    'skill_label'              => $skillLabel,
                ],
                'profile'         => $profileData,
                'user_skills'     => $userSkills,
                'quiz_by_subject' => $quizBySubject,
                'weak_areas'      => $weakAreas->values()->all(),
                'recommendations' => $recommendations,
                'recent_attempts' => $recentAttempts->values()->all(),
                'recent_activity' => $recentActivity->values()->all(),
            ];
        }); // end Cache::remember

        return response()->json([
            'success' => true,
            'message' => 'Dashboard overview retrieved.',
            'data'    => $data,
        ]);
    }
}
