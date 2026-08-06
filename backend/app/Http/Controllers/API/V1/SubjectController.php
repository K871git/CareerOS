<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubjectResource;
use App\Models\LearningTrack;
use Illuminate\Http\JsonResponse;

class SubjectController extends Controller
{
    public function index(LearningTrack $track): JsonResponse
    {
        $subjects = $track->subjects()->orderBy('display_order')->get();

        return response()->json([
            'success' => true,
            'message' => 'Subjects retrieved successfully.',
            'data'    => SubjectResource::collection($subjects),
        ]);
    }
}
