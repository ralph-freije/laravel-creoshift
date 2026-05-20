<?php

use App\Http\Controllers\Api\FlightController;
use App\Http\Controllers\Api\PassengerController;
use Illuminate\Support\Facades\Route;

Route::get('/passengers', [PassengerController::class, 'index']);

Route::get('/flights', [FlightController::class, 'index']);

Route::get('/flights/{flight}/passengers', [FlightController::class, 'passengers']);