<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TopicResource;
use App\Models\AssessmentAttempt;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    public function index(Request $request, Subject $subject): JsonResponse
    {
        $topics = $subject->topics()->orderBy('display_order')->get();
        $user   = $request->user();

        $topicIds  = $topics->pluck('id');
        $bestScores = AssessmentAttempt::where('user_id', $user->id)
            ->whereIn('topic_id', $topicIds)
            ->selectRaw('topic_id, MAX(score) as best_score')
            ->groupBy('topic_id')
            ->pluck('best_score', 'topic_id');

        foreach ($topics as $index => $topic) {
            $best = (int) ($bestScores[$topic->id] ?? 0);

            $topic->best_score   = $best;
            $topic->is_completed = $best >= 7;

            if ($index === 0) {
                $topic->is_locked = false;
            } else {
                $previousBest     = (int) ($bestScores[$topics[$index - 1]->id] ?? 0);
                $topic->is_locked = $previousBest < 7;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Topics retrieved successfully.',
            'data'    => TopicResource::collection($topics),
        ]);
    }
}
