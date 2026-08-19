<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;

Route::get('/health', fn() => response()->json([
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
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\SkillController;
use App\Http\Controllers\Api\V1\TopicController;
use App\Http\Controllers\Api\V1\LevelController;
use App\Http\Controllers\Api\V1\PlaygroundController;

Route::prefix('v1/auth')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);

    Route::post('/otp/send',   [AuthController::class, 'sendOtp']);
    Route::post('/otp/verify', [AuthController::class, 'verifyOtp']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me',      [AuthController::class, 'me']);
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

        // Level system — by-slug must come before model-bound {subject} routes
        Route::get('subjects/by-slug/{slug}', [LevelController::class, 'bySlug']);
        Route::get('subjects/{subject}/levels', [LevelController::class, 'index']);
        Route::get('subjects/{subject}/levels/{level}/topics', [LevelController::class, 'topics']);
        Route::get('subjects/{subject}/levels/{level}/exam', [LevelController::class, 'examQuestions']);
        Route::post('subjects/{subject}/levels/{level}/exam', [LevelController::class, 'submitExam']);

        Route::get('subjects/{subject}/topics', [TopicController::class, 'index']);
        Route::get('topics/{topic}/lessons', [LessonController::class, 'index']);
        Route::get('lessons/{lesson}', [LessonController::class, 'show']);

        Route::get('topics/{topic}/questions', [QuestionController::class, 'index']);
        Route::post('assessments/submit', [QuestionController::class, 'submit']);
        Route::get('assessments/{attempt}', [QuestionController::class, 'result']);

        Route::get('skills', [SkillController::class, 'index']);

        Route::get('dashboard', [DashboardController::class, 'overview']);

        Route::get('progress', [ProgressController::class, 'index']);
        Route::get('activity/recent', [ProgressController::class, 'recentActivity']);
        Route::post('lessons/{lesson}/complete', [ProgressController::class, 'completeLesson']);
        Route::get('tracks/{track}/progress', [ProgressController::class, 'trackProgress']);
    });


Route::middleware('auth:sanctum')->prefix('v1/playground')->group(function () {
    Route::post('/run', [PlaygroundController::class, 'run']);
});