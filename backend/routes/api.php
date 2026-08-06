<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;

Route::get('/health', fn () => response()->json([
    'status'  => 'ok',
    'service' => 'CareerOS API',
]));
use App\Http\Controllers\Api\V1\CareerAssessmentController;
use App\Http\Controllers\Api\V1\LearningTrackController;
use App\Http\Controllers\Api\V1\LessonController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\QuestionController;
use App\Http\Controllers\Api\V1\SubjectController;
use App\Http\Controllers\Api\V1\ProgressController;
use App\Http\Controllers\Api\V1\TheoryQuestionController;
use App\Http\Controllers\Api\V1\TopicController;

Route::prefix('v1/auth')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);

    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/me', [AuthController::class, 'me']);
    });
});

Route::middleware('auth:sanctum')->prefix('v1/profile')->group(function () {
    Route::get('/', [ProfileController::class, 'show']);
    Route::put('/', [ProfileController::class, 'update']);
});

Route::middleware('auth:sanctum')
    ->prefix('v1/career-assessment')
    ->group(function () {
        Route::get('/', [CareerAssessmentController::class, 'show']);
        Route::post('/', [CareerAssessmentController::class, 'store']);
        Route::put('/', [CareerAssessmentController::class, 'update']);
    });

Route::middleware('auth:sanctum')
    ->prefix('v1')
    ->group(function () {
        Route::apiResource('tracks', LearningTrackController::class)->only(['index', 'show']);
        Route::get('tracks/{track}/subjects', [SubjectController::class, 'index']);
        Route::get('subjects/{subject}/topics', [TopicController::class, 'index']);
        Route::get('topics/{topic}/lessons', [LessonController::class, 'index']);
        Route::get('lessons/{lesson}', [LessonController::class, 'show']);

        Route::get('topics/{topic}/questions', [QuestionController::class, 'index']);
        Route::post('assessments/submit', [QuestionController::class, 'submit']);
        Route::get('assessments/{attempt}', [QuestionController::class, 'result']);

        Route::get('topics/{topic}/theory-questions', [TheoryQuestionController::class, 'index']);
        Route::post('theory-questions/{question}/submit', [TheoryQuestionController::class, 'submit']);
        Route::get('theory-answers/{answer}', [TheoryQuestionController::class, 'show']);

        Route::get('progress', [ProgressController::class, 'index']);
        Route::post('lessons/{lesson}/complete', [ProgressController::class, 'completeLesson']);
        Route::get('tracks/{track}/progress', [ProgressController::class, 'trackProgress']);
    });
