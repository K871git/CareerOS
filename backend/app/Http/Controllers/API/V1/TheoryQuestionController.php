<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmitTheoryAnswerRequest;
use App\Http\Resources\TheoryAnswerResource;
use App\Http\Resources\TheoryQuestionResource;
use App\Models\Question;
use App\Models\TheoryAnswer;
use App\Models\Topic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TheoryQuestionController extends Controller
{
    public function index(Topic $topic): JsonResponse
    {
        $questions = $topic->questions()
            ->where('type', 'THEORY')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Theory questions retrieved successfully.',
            'data'    => TheoryQuestionResource::collection($questions),
        ]);
    }

    public function submit(SubmitTheoryAnswerRequest $request, Question $question): JsonResponse
    {
        if ($question->type !== 'THEORY') {
            return response()->json([
                'success' => false,
                'message' => 'This question is not a theory question.',
            ], 422);
        }

        $answer = TheoryAnswer::updateOrCreate(
            [
                'user_id'     => $request->user()->id,
                'question_id' => $question->id,
            ],
            [
                'answer'   => $request->validated('answer'),
                'status'   => 'pending_review',
                'feedback' => null,
                'score'    => null,
            ],
        );

        $answer->load('question');

        return response()->json([
            'success' => true,
            'message' => 'Answer submitted successfully. It is pending review.',
            'data'    => new TheoryAnswerResource($answer),
        ], 201);
    }

    public function show(Request $request, TheoryAnswer $answer): JsonResponse
    {
        if ($answer->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to this answer.',
            ], 403);
        }

        $answer->load('question');

        return response()->json([
            'success' => true,
            'message' => 'Theory answer retrieved successfully.',
            'data'    => new TheoryAnswerResource($answer),
        ]);
    }
}
