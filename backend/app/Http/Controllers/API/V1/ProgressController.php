<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProgressResource;
use App\Models\AssessmentAttempt;
use App\Models\LearningTrack;
use App\Models\Lesson;
use App\Models\LevelCompletion;
use App\Models\UserProgress;
use App\Services\UserAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProgressController extends Controller
{
    public function __construct(private UserAnalyticsService $analytics) {}

    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $data = Cache::remember("progress.overview.{$userId}", 30, function () use ($userId) {
            // --- Learning: SQL aggregation avoids loading the entire track tree into memory ---
            $tracks = DB::select("
                SELECT
                    lt.id,
                    lt.title,
                    lt.slug,
                    COUNT(DISTINCT l.id)                                                    AS total_lessons,
                    COUNT(DISTINCT CASE WHEN up.status = 'COMPLETED' THEN up.lesson_id END) AS completed_lessons
                FROM learning_tracks lt
                LEFT JOIN subjects s  ON s.learning_track_id = lt.id
                LEFT JOIN topics t    ON t.subject_id = s.id
                LEFT JOIN lessons l   ON l.topic_id = t.id
                LEFT JOIN user_progress up ON up.lesson_id = l.id AND up.user_id = ?
                GROUP BY lt.id, lt.title, lt.slug
                ORDER BY lt.display_order
            ", [$userId]);

            $totalLessons   = (int) array_sum(array_column($tracks, 'total_lessons'));
            $totalCompleted = (int) array_sum(array_column($tracks, 'completed_lessons'));

            $trackData = collect($tracks)->map(fn ($t) => [
                'id'                => $t->id,
                'title'             => $t->title,
                'slug'              => $t->slug,
                'total_lessons'     => (int) $t->total_lessons,
                'completed_lessons' => (int) $t->completed_lessons,
                'percentage'        => $t->total_lessons > 0
                    ? round($t->completed_lessons / $t->total_lessons * 100, 1)
                    : 0.0,
            ])->values()->all();

            $learningLevelsPassed = LevelCompletion::where('user_id', $userId)->where('passed', true)->count();

            // --- Practice: quiz stats via shared analytics service ---
            $quiz = $this->analytics->quizBySubject($userId);

            $quizzesTaken  = $quiz['quizzesTaken'];
            $totalAnswered = $quiz['totalAnswered'];
            $totalCorrect  = $quiz['totalCorrect'];
            $accuracy      = $quiz['accuracy'];

            $quizBySubject = collect($quiz['bySubject'])->map(fn ($d) => array_merge($d, [
                'accuracy' => $d['avg_score'],
            ]))->sortByDesc('accuracy')->values()->all();

            return [
                'summary' => [
                    'total_lessons'          => $totalLessons,
                    'completed_lessons'      => $totalCompleted,
                    'percentage'             => $totalLessons > 0
                        ? round($totalCompleted / $totalLessons * 100, 1)
                        : 0.0,
                    'quizzes_taken'          => $quizzesTaken,
                    'accuracy'               => $accuracy,
                    'learning_levels_passed' => $learningLevelsPassed,
                ],
                'tracks'   => $trackData,
                'practice' => [
                    'quizzes_taken'            => $quizzesTaken,
                    'total_questions_answered' => $totalAnswered,
                    'total_correct'            => $totalCorrect,
                    'accuracy'                 => $accuracy,
                    'by_subject'               => $quizBySubject,
                ],
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Progress dashboard retrieved successfully.',
            'data'    => $data,
        ]);
    }

    public function recentActivity(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $lessonActivities = UserProgress::where('user_id', $userId)
            ->where('status', 'COMPLETED')
            ->with(['lesson.topic.subject'])
            ->orderByDesc('completed_at')
            ->limit(20)
            ->get()
            ->map(fn ($p) => [
                'type'         => 'lesson_completed',
                'description'  => 'Completed: ' . ($p->lesson?->title ?? 'Lesson'),
                'subject_name' => $p->lesson?->topic?->subject?->title,
                'created_at'   => $p->completed_at?->toISOString() ?? $p->updated_at->toISOString(),
            ]);

        $quizActivities = AssessmentAttempt::where('user_id', $userId)
            ->with(['topic.subject'])
            ->orderByDesc('submitted_at')
            ->limit(20)
            ->get()
            ->map(fn ($a) => [
                'type'         => 'quiz_completed',
                'description'  => 'Quiz: ' . ($a->topic?->title ?? 'Practice') . ' — '
                    . ($a->total_questions > 0 ? round($a->score / $a->total_questions * 100) : 0) . '%',
                'subject_name' => $a->topic?->subject?->title,
                'score'        => $a->total_questions > 0
                    ? round($a->score / $a->total_questions * 100, 1)
                    : 0.0,
                'created_at'   => $a->submitted_at?->toISOString() ?? $a->updated_at->toISOString(),
            ]);

        $activities = $lessonActivities
            ->concat($quizActivities)
            ->filter(fn ($a) => $a['created_at'] !== null)
            ->sortByDesc('created_at')
            ->take(15)
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Recent activity retrieved successfully.',
            'data'    => $activities,
        ]);
    }

    public function completeLesson(Request $request, Lesson $lesson): JsonResponse
    {
        $userId = $request->user()->id;

        $progress = UserProgress::updateOrCreate(
            ['user_id' => $userId, 'lesson_id' => $lesson->id],
            ['status' => 'COMPLETED', 'completed_at' => now()],
        );

        Cache::forget("progress.overview.{$userId}");
        Cache::forget("dashboard.overview.{$userId}");

        $progress->load('lesson');

        return response()->json([
            'success' => true,
            'message' => 'Lesson marked as completed.',
            'data'    => new ProgressResource($progress),
        ]);
    }

    public function trackProgress(Request $request, LearningTrack $track): JsonResponse
    {
        $userId = $request->user()->id;

        $track->load('subjects.topics.lessons');

        $allLessonIds = $track->subjects
            ->flatMap(fn($s) => $s->topics->flatMap(fn($t) => $t->lessons->pluck('id')));

        $progressMap = UserProgress::where('user_id', $userId)
            ->whereIn('lesson_id', $allLessonIds)
            ->get()
            ->keyBy('lesson_id');

        $countCompleted = fn($lessonIds) => $lessonIds
            ->filter(fn($id) => $progressMap->get($id)?->status === 'COMPLETED')
            ->count();

        $trackTotal     = $allLessonIds->count();
        $trackCompleted = $countCompleted($allLessonIds);

        $subjects = $track->subjects->map(function ($subject) use ($progressMap, $countCompleted) {
            $subjectLessonIds = $subject->topics->flatMap(fn($t) => $t->lessons->pluck('id'));
            $subjectTotal     = $subjectLessonIds->count();
            $subjectCompleted = $countCompleted($subjectLessonIds);

            $topics = $subject->topics->map(function ($topic) use ($progressMap, $countCompleted) {
                $topicLessonIds = $topic->lessons->pluck('id');
                $topicTotal     = $topicLessonIds->count();
                $topicCompleted = $countCompleted($topicLessonIds);

                $lessons = $topic->lessons->map(fn($lesson) => [
                    'id'           => $lesson->id,
                    'title'        => $lesson->title,
                    'status'       => $progressMap->get($lesson->id)?->status ?? 'NOT_STARTED',
                    'completed_at' => $progressMap->get($lesson->id)?->completed_at,
                ]);

                return [
                    'id'                => $topic->id,
                    'title'             => $topic->title,
                    'total_lessons'     => $topicTotal,
                    'completed_lessons' => $topicCompleted,
                    'percentage'        => $topicTotal > 0 ? round($topicCompleted / $topicTotal * 100, 1) : 0.0,
                    'lessons'           => $lessons,
                ];
            });

            return [
                'id'                => $subject->id,
                'title'             => $subject->title,
                'total_lessons'     => $subjectTotal,
                'completed_lessons' => $subjectCompleted,
                'percentage'        => $subjectTotal > 0 ? round($subjectCompleted / $subjectTotal * 100, 1) : 0.0,
                'topics'            => $topics,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Track progress retrieved successfully.',
            'data'    => [
                'track' => [
                    'id'                => $track->id,
                    'title'             => $track->title,
                    'slug'              => $track->slug,
                    'total_lessons'     => $trackTotal,
                    'completed_lessons' => $trackCompleted,
                    'percentage'        => $trackTotal > 0 ? round($trackCompleted / $trackTotal * 100, 1) : 0.0,
                ],
                'subjects' => $subjects,
            ],
        ]);
    }
}
