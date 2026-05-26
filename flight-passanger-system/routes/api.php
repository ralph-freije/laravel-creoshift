<?php

use App\Http\Controllers\Api\FlightController;
use App\Http\Controllers\Api\PassengerController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/passengers', [PassengerController::class, 'index']);
Route::post('/passengers', [PassengerController::class, 'store']);
Route::get('/passengers/{passenger}', [PassengerController::class, 'show']);
Route::put('/passengers/{passenger}', [PassengerController::class, 'update']);
Route::delete('/passengers/{passenger}', [PassengerController::class, 'destroy']);

Route::get('/flights', [FlightController::class, 'index']);

Route::apiResource('/users', UserController::class);