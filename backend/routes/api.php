<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FlashCardSetController;
use App\Http\Controllers\GoogleTTSController;

Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/changePassword', [AuthController::class, 'changePassword'])->middleware('auth:sanctum');

Route::apiResource('/flashCard', FlashCardSetController::class)->middleware('auth:sanctum')->except(['show']);
Route::prefix('flashCard')->group(function () {
    Route::get('/public', [FlashCardSetController::class, 'publicIndex']);
    Route::get('/{id}', [FlashCardSetController::class, 'show']);
    Route::get('/details/{id}', [FlashCardSetController::class, 'showDetails']);
    Route::get('/edit/{id}', [FlashCardSetController::class, 'edit'])->middleware('auth:sanctum');
});

Route::prefix('google-tts')->group(function () {
    Route::get('/status', [GoogleTTSController::class, 'status']);
    Route::get('/languages', [GoogleTTSController::class, 'listLanguages']);
    Route::post('/synthesize', [GoogleTTSController::class, 'synthesize']);
});
