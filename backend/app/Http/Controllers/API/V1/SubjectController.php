<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubjectResource;
use App\Models\LearningTrack;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    public function index(LearningTrack $track): JsonResponse
    {
        $subjects = $track->subjects()->orderBy('display_order')->get();

        $subjectIds = $subjects->pluck('id');
        $mcqCounts  = DB::table('topics')
            ->join('questions', 'questions.topic_id', '=', 'topics.id')
            ->whereIn('topics.subject_id', $subjectIds)
            ->where('questions.type', 'MCQ')
            ->groupBy('topics.subject_id')
            ->selectRaw('topics.subject_id, COUNT(questions.id) as cnt')
            ->pluck('cnt', 'topics.subject_id');

        foreach ($subjects as $subject) {
            $subject->mcq_question_count = (int) ($mcqCounts[$subject->id] ?? 0);
        }

        return response()->json([
            'success' => true,
            'message' => 'Subjects retrieved successfully.',
            'data'    => SubjectResource::collection($subjects),
        ]);
    }
}
