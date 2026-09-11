<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\Notification;
use App\Models\SensorReading;
use Illuminate\Http\Request;

class SensorReadingController extends Controller
{
    public function store(Request $request)
    {
        // Token authentication for ESP32 devices
        $token = $request->header('X-ESP32-Token');
        if ($token !== config('app.esp32_secret')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->validate([
            'bus_id'          => 'required|exists:buses,id',
            'temperature'     => 'nullable|numeric',
            'oil_temperature' => 'nullable|numeric',
            'vibration'       => 'nullable|numeric',
            'oil_pressure'    => 'nullable|numeric',
            'battery_voltage' => 'nullable|numeric',
        ]);

        if(!$data) {
            return response()->json([
                'error' => 'All Fields Required'
            ]);
        };

        $data['recorded_at'] = now();

        $reading = SensorReading::create($data);

        // Generate alerts for out-of-range values
        $bus = Bus::find($data['bus_id']);
        $health = $reading->getHealthStatus();

        if ($health === 'critical') {
            Notification::create([
                'title'   => "Critical Alert: Bus {$bus->registration_number}",
                'message' => "Sensor readings critical — Coolant: {$reading->temperature}°C, Oil: {$reading->oil_temperature}°C, Vibration: {$reading->vibration}g, Oil Pressure: {$reading->oil_pressure}PSI. Immediate inspection required.",
                'type'    => 'alert',
            ]);
        } elseif ($health === 'warning') {
            Notification::create([
                'title'   => "Warning: Bus {$bus->registration_number}",
                'message' => "Sensor readings out of normal range — Coolant: {$reading->temperature}°C, Oil: {$reading->oil_temperature}°C, Vibration: {$reading->vibration}g. Monitor closely.",
                'type'    => 'warning',
            ]);
        }

        return response()->json([
            'status'  => 'ok',
            'health'  => $health,
            'reading' => $reading,
        ], 201);
    }
}
