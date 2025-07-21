<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{TravelController, AuthController};

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::prefix('travels')->group(function () {
        Route::get('/', [TravelController::class, 'index']);
        Route::get('/{id}', [TravelController::class, 'show']);
        Route::post('/', [TravelController::class, 'store']);
        Route::patch('/{id}/status', [TravelController::class, 'updateStatus']);
        Route::patch('/{id}/cancel', [TravelController::class, 'cancel']);
    });
});
