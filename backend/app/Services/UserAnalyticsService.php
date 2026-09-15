<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class UserAnalyticsService
{
    /**
     * SQL-aggregated quiz statistics per subject for a user.
     *
     * Returns summary totals + a by_subject breakdown, sorted by avg_score desc.
     */
    public function quizBySubject(int $userId): array
    {
        $rows = DB::select("
            SELECT
                s.id                    AS subject_id,
                s.title                 AS subject_title,
                COUNT(aa.id)            AS attempts,
                SUM(aa.total_questions) AS total_questions,
                SUM(aa.score)           AS total_correct
            FROM assessment_attempts aa
            JOIN topics t   ON t.id  = aa.topic_id
            JOIN subjects s ON s.id  = t.subject_id
            WHERE aa.user_id = ?
            GROUP BY s.id, s.title
        ", [$userId]);

        $quizzesTaken  = (int) array_sum(array_column($rows, 'attempts'));
        $totalAnswered = (int) array_sum(array_column($rows, 'total_questions'));
        $totalCorrect  = (int) array_sum(array_column($rows, 'total_correct'));
        $accuracy      = $totalAnswered > 0
            ? round($totalCorrect / $totalAnswered * 100, 1)
            : 0.0;

        $bySubject = collect($rows)->map(fn ($d) => [
            'subject_id'      => $d->subject_id,
            'subject_title'   => $d->subject_title,
            'attempts'        => (int) $d->attempts,
            'total_questions' => (int) $d->total_questions,
            'total_correct'   => (int) $d->total_correct,
            'avg_score'       => $d->total_questions > 0
                ? round($d->total_correct / $d->total_questions * 100, 1)
                : 0.0,
        ])->sortByDesc('avg_score')->values()->all();

        return compact('quizzesTaken', 'totalAnswered', 'totalCorrect', 'accuracy', 'bySubject');
    }

    /**
     * SQL-aggregated quiz statistics per topic for a user.
     *
     * Used for weak-area detection (topics scoring below 70%).
     */
    public function quizByTopic(int $userId): array
    {
        $rows = DB::select("
            SELECT
                t.id                    AS topic_id,
                t.title                 AS topic_title,
                t.slug                  AS topic_slug,
                s.title                 AS subject_title,
                COUNT(aa.id)            AS attempts,
                SUM(aa.total_questions) AS total_questions,
                SUM(aa.score)           AS total_correct
            FROM assessment_attempts aa
            JOIN topics t   ON t.id  = aa.topic_id
            JOIN subjects s ON s.id  = t.subject_id
            WHERE aa.user_id = ?
            GROUP BY t.id, t.title, t.slug, s.title
        ", [$userId]);

        return collect($rows)->map(fn ($d) => [
            'topic_id'        => $d->topic_id,
            'topic_title'     => $d->topic_title,
            'topic_slug'      => $d->topic_slug,
            'subject_title'   => $d->subject_title,
            'attempts'        => (int) $d->attempts,
            'total_questions' => (int) $d->total_questions,
            'total_correct'   => (int) $d->total_correct,
            'avg_score'       => $d->total_questions > 0
                ? round($d->total_correct / $d->total_questions * 100, 1)
                : 0.0,
        ])->all();
    }
}
