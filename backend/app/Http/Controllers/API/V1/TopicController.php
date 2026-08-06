<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TopicResource;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;

class TopicController extends Controller
{
    public function index(Subject $subject): JsonResponse
    {
        $topics = $subject->topics()->orderBy('display_order')->get();

        return response()->json([
            'success' => true,
            'message' => 'Topics retrieved successfully.',
            'data'    => TopicResource::collection($topics),
        ]);
    }
}
