<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\LearningTrackResource;
use App\Models\LearningTrack;
use Illuminate\Http\JsonResponse;

class LearningTrackController extends Controller
{
    public function index(): JsonResponse
    {
        $tracks = LearningTrack::orderBy('display_order')->get();


        return response()->json([
            'success' => true,
            'message' => 'Learning tracks retrieved successfully.',
            'data'    => LearningTrackResource::collection($tracks),
        ]);
    }

    public function show(LearningTrack $track): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Learning track retrieved successfully.',
            'data'    => new LearningTrackResource($track),
        ]);
    }
}
