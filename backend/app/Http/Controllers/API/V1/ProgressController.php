<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProgressResource;
use App\Models\AssessmentAttempt;
use App\Models\LearningTrack;
use App\Models\Lesson;
use App\Models\LevelCompletion;
use App\Models\TheoryCompletion;
use App\Models\UserProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    private const THEORY_AREAS = ['languages', 'frameworks', 'networking'];

    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        // --- Learning: tracks + lessons ---
        $tracks = LearningTrack::with(['subjects.topics.lessons'])->get();

        $completedIds = UserProgress::where('user_id', $userId)
            ->where('status', 'COMPLETED')
            ->pluck('lesson_id');

        $totalLessons   = 0;
        $totalCompleted = 0;
        $trackData      = [];

        foreach ($tracks as $track) {
            $lessonIds = $track->subjects
                ->flatMap(fn ($s) => $s->topics->flatMap(fn ($t) => $t->lessons->pluck('id')));

            $trackTotal     = $lessonIds->count();
            $trackCompleted = $lessonIds->intersect($completedIds)->count();

            $totalLessons   += $trackTotal;
            $totalCompleted += $trackCompleted;

            $trackData[] = [
                'id'                => $track->id,
                'title'             => $track->title,
                'slug'              => $track->slug,
                'total_lessons'     => $trackTotal,
                'completed_lessons' => $trackCompleted,
                'percentage'        => $trackTotal > 0 ? round($trackCompleted / $trackTotal * 100, 1) : 0.0,
            ];
        }

        $learningLevelsPassed = LevelCompletion::where('user_id', $userId)->where('passed', true)->count();

        // --- Practice: quiz attempts ---
        $practiceAttempts = AssessmentAttempt::where('user_id', $userId)
            ->with(['topic.subject'])
            ->orderByDesc('submitted_at')
            ->get();

        $quizzesTaken  = $practiceAttempts->count();
        $totalAnswered = $practiceAttempts->sum('total_questions');
        $totalCorrect  = $practiceAttempts->sum('score');
        $accuracy      = $totalAnswered > 0
            ? round($totalCorrect / $totalAnswered * 100, 1)
            : 0.0;

        $subjectMap = [];
        foreach ($practiceAttempts as $attempt) {
            $subject = $attempt->topic?->subject;
            if (! $subject) {
                continue;
            }
            $sid = $subject->id;
            if (! isset($subjectMap[$sid])) {
                $subjectMap[$sid] = [
                    'subject_id'      => $sid,
                    'subject_title'   => $subject->title,
                    'attempts'        => 0,
                    'total_questions' => 0,
                    'total_correct'   => 0,
                ];
            }
            $subjectMap[$sid]['attempts']++;
            $subjectMap[$sid]['total_questions'] += $attempt->total_questions;
            $subjectMap[$sid]['total_correct']   += $attempt->score;
        }

        $quizBySubject = collect($subjectMap)->map(fn ($d) => array_merge($d, [
            'accuracy' => $d['total_questions'] > 0
                ? round($d['total_correct'] / $d['total_questions'] * 100, 1)
                : 0.0,
        ]))->sortByDesc('accuracy')->values();

        // --- Theory: level completions ---
        $theoryCompletions   = TheoryCompletion::where('user_id', $userId)->where('passed', true)->get();
        $theoryLevelsPassed  = $theoryCompletions->count();
        $theoryLevelsTotal   = count(self::THEORY_AREAS) * 3;

        $theoryByArea = collect(self::THEORY_AREAS)->map(fn ($area) => [
            'area'   => $area,
            'passed' => $theoryCompletions->where('area', $area)->count(),
            'total'  => 3,
        ])->values();

        return response()->json([
            'success' => true,
            'message' => 'Progress dashboard retrieved successfully.',
            'data'    => [
                'summary' => [
                    'total_lessons'          => $totalLessons,
                    'completed_lessons'      => $totalCompleted,
                    'percentage'             => $totalLessons > 0
                        ? round($totalCompleted / $totalLessons * 100, 1)
                        : 0.0,
                    'quizzes_taken'          => $quizzesTaken,
                    'accuracy'               => $accuracy,
                    'theory_levels_passed'   => $theoryLevelsPassed,
                    'theory_levels_total'    => $theoryLevelsTotal,
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
                'theory' => [
                    'levels_passed' => $theoryLevelsPassed,
                    'levels_total'  => $theoryLevelsTotal,
                    'by_area'       => $theoryByArea,
                ],
            ],
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

        $theoryActivities = TheoryCompletion::where('user_id', $userId)
            ->where('passed', true)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->map(fn ($t) => [
                'type'         => 'theory_passed',
                'description'  => ucfirst($t->area) . ' — Level ' . $t->level . ' passed',
                'subject_name' => ucfirst($t->area),
                'created_at'   => $t->created_at?->toISOString(),
            ]);

        $activities = $lessonActivities
            ->concat($quizActivities)
            ->concat($theoryActivities)
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
        $progress = UserProgress::updateOrCreate(
            [
                'user_id'   => $request->user()->id,
                'lesson_id' => $lesson->id,
            ],
            [
                'status'       => 'COMPLETED',
                'completed_at' => now(),
            ],
        );

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
