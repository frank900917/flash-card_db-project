<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\GoogleTTSController;
use App\Http\Controllers\FlashCardSetController;
use App\Http\Controllers\TestRecordController;

Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/changePassword', [AuthController::class, 'changePassword'])->middleware('auth:sanctum');
Route::post('/testRecord', [TestRecordController::class, 'store'])->middleware('auth:sanctum');
Route::get('/testRecord', [TestRecordController::class, 'index'])->middleware('auth:sanctum');

Route::apiResource('/flashCard', FlashCardSetController::class)->middleware('auth:sanctum')->except(['show']);
Route::prefix('flashCard')->group(function () {
    Route::get('/public', [FlashCardSetController::class, 'publicIndex']);
    Route::get('/{id}', [FlashCardSetController::class, 'show']);
    Route::get('/details/{id}', [FlashCardSetController::class, 'showDetails']);
    Route::get('/edit/{id}', [FlashCardSetController::class, 'edit'])->middleware('auth:sanctum');

    # TODO: get card theme
    Route::get('/{flashCardId}/theme', [ThemeController::class, 'getCurrentTheme'])->middleware('auth:sanctum');
    # TODO: get theme list
    Route::get('/theme', [ThemeController::class, 'getThemeList'])->middleware('auth:sanctum');
    # TODO: update theme
    # put 時 body 要附上 {theme_id: XX}
    Route::put('/{flashCardId}/theme', [ThemeController::class, 'updateTheme'])->middleware('auth:sanctum');
});

Route::prefix('google-tts')->group(function () {
    Route::get('/status', [GoogleTTSController::class, 'status']);
    Route::get('/languages', [GoogleTTSController::class, 'listLanguages']);
    Route::post('/synthesize', [GoogleTTSController::class, 'synthesize']);
});