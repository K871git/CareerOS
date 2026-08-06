<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProgressResource;
use App\Models\LearningTrack;
use App\Models\Lesson;
use App\Models\UserProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $tracks = LearningTrack::with(['subjects.topics.lessons'])->get();

        $completedIds = UserProgress::where('user_id', $userId)
            ->where('status', 'COMPLETED')
            ->pluck('lesson_id');

        $totalLessons   = 0;
        $totalCompleted = 0;
        $trackData      = [];

        foreach ($tracks as $track) {
            $lessonIds = $track->subjects
                ->flatMap(fn($s) => $s->topics->flatMap(fn($t) => $t->lessons->pluck('id')));

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

        return response()->json([
            'success' => true,
            'message' => 'Progress dashboard retrieved successfully.',
            'data'    => [
                'summary' => [
                    'total_lessons'     => $totalLessons,
                    'completed_lessons' => $totalCompleted,
                    'percentage'        => $totalLessons > 0 ? round($totalCompleted / $totalLessons * 100, 1) : 0.0,
                ],
                'tracks' => $trackData,
            ],
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
