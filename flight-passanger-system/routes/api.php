<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FlightController;
use App\Http\Controllers\Api\PassengerController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('/passengers', PassengerController::class)->only([
        'index',
        'show',
    ]);

    Route::apiResource('/flights', FlightController::class)->only([
        'index',
        'show',
    ]);

    Route::apiResource('/users', UserController::class)->only([
        'index',
        'show',
    ]);

    Route::middleware('role:admin')->group(function () {
        Route::apiResource('/passengers', PassengerController::class)->except([
            'index',
            'show',
        ]);

        Route::apiResource('/flights', FlightController::class)->except([
            'index',
            'show',
        ]);

        Route::apiResource('/users', UserController::class)->except([
            'index',
            'show',
        ]);
    });
});