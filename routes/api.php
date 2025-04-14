<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CouriersController;
use App\Http\Controllers\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/couriers/{id}/vote', [CouriersController::class, 'vote']);
    Route::delete('/couriers/{id}/unvote', [CouriersController::class, 'unvote']);
});

Route::get('/couriers', [CouriersController::class, 'index']);
Route::get('/couriers/{courier}', [CouriersController::class, 'show']);
