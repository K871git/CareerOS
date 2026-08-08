<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\JsonResponse;

class SkillController extends Controller
{
    public function index(): JsonResponse
    {
        $skills = Skill::orderBy('category')->orderBy('name')->get(['id', 'name', 'slug', 'category']);

        return response()->json([
            'success' => true,
            'message' => 'Skills retrieved successfully.',
            'data'    => $skills,
        ]);
    }
}
