<?php

use App\Http\Controllers\Api\SensorReadingController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// ESP32 sensor data endpoint — authenticated via X-ESP32-Token header
Route::post('/sensor-readings', [SensorReadingController::class, 'store']);

// Uptime check: is the app answering and is the database reachable? (/up only
// covers the former.) Returns 200 or 503 with no configuration or error details.
Route::get('/health', function () {
    try {
        DB::select('select 1');
        $database = 'ok';
    } catch (\Throwable $e) {
        report($e);
        $database = 'unavailable';
    }

    return response()->json(
        ['status' => $database === 'ok' ? 'ok' : 'unavailable', 'database' => $database],
        $database === 'ok' ? 200 : 503,
    );
});
