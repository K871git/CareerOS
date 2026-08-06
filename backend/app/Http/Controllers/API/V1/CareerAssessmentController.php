<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCareerAssessmentRequest;
use App\Http\Resources\CareerAssessmentResource;
use App\Models\UserSkill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CareerAssessmentController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user    = $request->user();
        $skills  = $user->skills()->with('skill')->get();
        $profile = $user->profile;

        return response()->json([
            'success' => true,
            'message' => 'Career assessment retrieved successfully.',
            'data'    => [
                'target_role' => $profile?->target_role,
                'skills'      => CareerAssessmentResource::collection($skills),
            ],
        ]);
    }

    public function store(StoreCareerAssessmentRequest $request): JsonResponse
    {
        return $this->upsertAssessment($request, 'Career assessment saved successfully.', 201);
    }

    public function update(StoreCareerAssessmentRequest $request): JsonResponse
    {
        return $this->upsertAssessment($request, 'Career assessment updated successfully.');
    }

    private function upsertAssessment(StoreCareerAssessmentRequest $request, string $message, int $status = 200): JsonResponse
    {
        $validated = $request->validated();
        $user      = $request->user();

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            ['target_role' => $validated['target_role']],
        );

        $records = collect($validated['skills'])->map(fn ($skill) => [
            'user_id'    => $user->id,
            'skill_id'   => $skill['skill_id'],
            'level'      => $skill['level'],
            'score'      => $skill['score'],
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray();

        UserSkill::upsert($records, ['user_id', 'skill_id'], ['level', 'score', 'updated_at']);

        $skills = $user->skills()->with('skill')->get();

        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => [
                'target_role' => $validated['target_role'],
                'skills'      => CareerAssessmentResource::collection($skills),
            ],
        ], $status);
    }
}
