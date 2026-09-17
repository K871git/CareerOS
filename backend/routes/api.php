<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ConsentController;

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
use App\Http\Controllers\Api\V1\CodingProblemController;
use App\Http\Controllers\Api\V1\SocialAuthController;
use App\Http\Controllers\Api\V1\TheoryLevelController;
use App\Http\Controllers\Api\V1\TheoryQuestionController;
use App\Http\Controllers\Api\V1\AiController;
use App\Http\Controllers\Api\V1\HintController;
use App\Http\Controllers\Api\V1\PointController;

Route::middleware('throttle:5,1')->prefix('v1/auth')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);

    Route::post('/otp/send',   [AuthController::class, 'sendOtp']);
    Route::post('/otp/verify', [AuthController::class, 'verifyOtp']);

    // logout stays throttled to protect against token-cycling attacks
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
});

// Password reset — separate from the 5/min auth throttle; Laravel's broker adds its own 60s cooldown
Route::middleware('throttle:10,1')->prefix('v1/auth')->group(function () {
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password',  [AuthController::class, 'resetPassword']);
});

// /me is called on every app mount — must not share the 5/min auth throttle
Route::middleware('auth:sanctum')->get('/v1/auth/me', [AuthController::class, 'me']);

// DPDP Act 2023 — Consent management (authenticated)
Route::middleware('auth:sanctum')->prefix('v1/consent')->group(function () {
    Route::post('/',        [ConsentController::class, 'store']);    // record / re-accept
    Route::delete('/',     [ConsentController::class, 'withdraw']); // withdraw consent
});

Route::get('/v1/auth/google',          [SocialAuthController::class, 'redirectToGoogle']);
Route::get('/v1/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);


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
        Route::middleware('throttle:10,1')->post('assessments/submit', [QuestionController::class, 'submit']);
        Route::get('assessments/{attempt}', [QuestionController::class, 'result']);

        Route::get('skills', [SkillController::class, 'index']);

        Route::get('dashboard', [DashboardController::class, 'overview']);

        Route::get('progress', [ProgressController::class, 'index']);
        Route::get('activity/recent', [ProgressController::class, 'recentActivity']);
        Route::post('lessons/{lesson}/complete', [ProgressController::class, 'completeLesson']);
        Route::get('tracks/{track}/progress', [ProgressController::class, 'trackProgress']);

        // Theory level system (MCQ-based, area → levels → exam)
        Route::get('theory/areas',                        [TheoryLevelController::class, 'areas']);
        Route::get('theory/{area}/levels',                [TheoryLevelController::class, 'levels']);
        Route::get('theory/{area}/levels/{level}/exam',                                      [TheoryLevelController::class, 'examQuestions']);
        Route::middleware('throttle:10,1')->post('theory/{area}/levels/{level}/exam',  [TheoryLevelController::class, 'submitExam']);

        // Theory Q&A (topic-based, written answers pending review)
        Route::get('topics/{topic}/theory-questions',     [TheoryQuestionController::class, 'index']);
        Route::post('theory-questions/{question}/submit', [TheoryQuestionController::class, 'submit']);
        Route::get('theory-answers/{answer}',             [TheoryQuestionController::class, 'show']);
    });


Route::middleware(['auth:sanctum', 'throttle:20,1'])->prefix('v1/playground')->group(function () {
    Route::post('/run',        [PlaygroundController::class, 'run']);
    Route::get('/schema',      [PlaygroundController::class, 'schema']);
    Route::post('/reset-data', [PlaygroundController::class, 'resetData']);
});

Route::middleware(['auth:sanctum', 'throttle:15,1'])->prefix('v1/battleground')->group(function () {
    Route::get('/problems',                              [CodingProblemController::class, 'index']);
    Route::get('/problems/{codingProblem:slug}',         [CodingProblemController::class, 'show']);
    Route::post('/problems/{codingProblem:slug}/submit', [CodingProblemController::class, 'submit']);
    Route::get('/problems/{codingProblem:slug}/submissions', [CodingProblemController::class, 'submissions']);
});

Route::middleware(['auth:sanctum', 'throttle:20,1'])
    ->post('v1/ai/explain', [AiController::class, 'explain']);

Route::middleware(['auth:sanctum', 'throttle:20,1'])
    ->post('v1/ai/explain/stream', [AiController::class, 'explainStream']);

// Points & Hints
Route::middleware('auth:sanctum')->prefix('v1/points')->group(function () {
    Route::get('/',        [PointController::class, 'balance']);
    Route::get('/history', [PointController::class, 'history']);
});

Route::middleware(['auth:sanctum', 'throttle:20,1'])
    ->post('v1/hints/unlock', [HintController::class, 'unlock']);
