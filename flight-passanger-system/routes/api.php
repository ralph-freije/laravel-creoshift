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
Route::get('/flights/{flight}/passengers', [FlightController::class, 'passengers']);

Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
Route::get('/users/{user}', [UserController::class, 'show']);
Route::put('/users/{user}', [UserController::class, 'update']);
Route::delete('/users/{user}', [UserController::class, 'destroy']);