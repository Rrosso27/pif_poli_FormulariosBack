<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SurveysController;
use App\Http\Controllers\QuestionsController;
use App\Http\Controllers\ResponseController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rutas públicas de autenticación
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas que requieren autenticación
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);

    // Rutas de encuestas protegidas
    Route::group(['prefix' => 'surveys'], function () {
        Route::get('/', [SurveysController::class, 'index']);
        Route::get('/{id}', [SurveysController::class, 'show']);
        Route::post('/', [SurveysController::class, 'store']);
        Route::put('/{id}', [SurveysController::class, 'update']);
        Route::delete('/{id}', [SurveysController::class, 'destroy']);
    });

    Route::group(['prefix' => 'questions'], function () {
        Route::get('/', [QuestionsController::class, 'index']);
        Route::get('/{id}', [QuestionsController::class, 'show']);
        Route::post('/', [QuestionsController::class, 'store']);
        Route::put('/{id}', [QuestionsController::class, 'update']);
        Route::delete('/{id}', [QuestionsController::class, 'destroy']);
        // Ruta para obtener todas las preguntas de una encuesta específica
        Route::get('/survey/{surveyId}', [QuestionsController::class, 'getAllBySurveyId']);
    });

    // Rutas de respuestas protegidas
    Route::group(['prefix' => 'responses'], function () {
        // Rutas específicas PRIMERO (antes de las rutas con parámetros dinámicos)
        Route::get('/my/{id}', [ResponseController::class, 'getByAuthUser']);
        Route::get('/debug-auth', [ResponseController::class, 'debugAuth']);

        // Rutas con parámetros dinámicos DESPUÉS
        Route::get('/{id}', [ResponseController::class, 'show']);
        Route::post('/', [ResponseController::class, 'store']);
        Route::put('/{id}', [ResponseController::class, 'update']);
        Route::delete('/{id}', [ResponseController::class, 'destroy']);
    });
});
