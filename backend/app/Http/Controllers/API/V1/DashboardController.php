<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AssessmentAttempt;
use App\Models\Lesson;
use App\Models\UserProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function overview(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        // --- Lesson progress ---
        $totalLessons     = Lesson::count();
        $completedLessons = UserProgress::where('user_id', $userId)
            ->where('status', 'COMPLETED')
            ->count();

        // --- All quiz attempts for this user ---
        $attempts = AssessmentAttempt::where('user_id', $userId)
            ->with(['topic.subject'])
            ->orderByDesc('submitted_at')
            ->get();

        $quizzesTaken  = $attempts->count();
        $totalAnswered = $attempts->sum('total_questions');
        $totalCorrect  = $attempts->sum('score');

        $accuracy = $totalAnswered > 0
            ? round($totalCorrect / $totalAnswered * 100, 1)
            : 0.0;

        $avgQuizScore = $quizzesTaken > 0
            ? round(
                $attempts->avg(fn ($a) => $a->total_questions > 0
                    ? ($a->score / $a->total_questions * 100)
                    : 0
                ),
                1
            )
            : 0.0;

        // --- Per-subject quiz breakdown ---
        $subjectMap = [];
        foreach ($attempts as $attempt) {
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
            'avg_score' => $d['total_questions'] > 0
                ? round($d['total_correct'] / $d['total_questions'] * 100, 1)
                : 0.0,
        ]))->sortByDesc('avg_score')->values();

        // --- Per-topic performance (used for weak area detection) ---
        $topicMap = [];
        foreach ($attempts as $attempt) {
            $topic = $attempt->topic;
            if (! $topic) {
                continue;
            }
            $tid = $topic->id;
            if (! isset($topicMap[$tid])) {
                $topicMap[$tid] = [
                    'topic_id'        => $tid,
                    'topic_title'     => $topic->title,
                    'topic_slug'      => $topic->slug,
                    'subject_title'   => $topic->subject?->title ?? '',
                    'attempts'        => 0,
                    'total_questions' => 0,
                    'total_correct'   => 0,
                ];
            }
            $topicMap[$tid]['attempts']++;
            $topicMap[$tid]['total_questions'] += $attempt->total_questions;
            $topicMap[$tid]['total_correct']   += $attempt->score;
        }

        // Weak areas = topics attempted with avg score < 70%
        $weakAreas = collect($topicMap)
            ->map(fn ($d) => array_merge($d, [
                'avg_score' => $d['total_questions'] > 0
                    ? round($d['total_correct'] / $d['total_questions'] * 100, 1)
                    : 0.0,
            ]))
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
                'description'   => 'Head to Practice and answer questions to start tracking your progress.',
                'topic_id'      => null,
                'topic_slug'    => null,
                'subject_title' => null,
            ];
        } else {
            foreach ($weakAreas->take(3) as $area) {
                $recommendations[] = [
                    'type'          => 'weak_topic',
                    'title'         => 'Revisit: ' . $area['topic_title'],
                    'description'   => 'Your score is ' . $area['avg_score'] . '% — retry this topic to level up.',
                    'topic_id'      => $area['topic_id'],
                    'topic_slug'    => $area['topic_slug'],
                    'subject_title' => $area['subject_title'],
                ];
            }

            if (empty($recommendations)) {
                $recommendations[] = [
                    'type'          => 'explore',
                    'title'         => 'All topics above 70% — great work!',
                    'description'   => 'Try a harder level or explore a new subject to keep improving.',
                    'topic_id'      => null,
                    'topic_slug'    => null,
                    'subject_title' => null,
                ];
            }
        }

        // --- Recent quiz attempts (last 5) ---
        $recentAttempts = $attempts->take(5)->map(fn ($attempt) => [
            'attempt_id'      => $attempt->id,
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

        return response()->json([
            'success' => true,
            'message' => 'Dashboard overview retrieved.',
            'data'    => [
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
                ],
                'quiz_by_subject' => $quizBySubject,
                'weak_areas'      => $weakAreas->values(),
                'recommendations' => $recommendations,
                'recent_attempts' => $recentAttempts,
                'recent_activity' => $recentActivity,
            ],
        ]);
    }
}
