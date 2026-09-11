<?php

use App\Http\Controllers\Api\SensorReadingController;
use Illuminate\Support\Facades\Route;

// ESP32 sensor data endpoint — authenticated via X-ESP32-Token header
Route::post('/sensor-readings', [SensorReadingController::class, 'store']);
