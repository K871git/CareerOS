<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\LessonResource;
use App\Models\Lesson;
use App\Models\Topic;
use Illuminate\Http\JsonResponse;

class LessonController extends Controller
{
    public function index(Topic $topic): JsonResponse
    {
        $lessons = $topic->lessons()->orderBy('display_order')->get();

        return response()->json([
            'success' => true,
            'message' => 'Lessons retrieved successfully.',
            'data'    => LessonResource::collection($lessons),
        ]);
    }

    public function show(Lesson $lesson): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Lesson retrieved successfully.',
            'data'    => new LessonResource($lesson),
        ]);
    }
}
