<?php

namespace App\Services;

use App\Models\Bus;
use App\Models\FailurePrediction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PredictionService
{
    private string $flaskUrl;

    public function __construct()
    {
        $this->flaskUrl = config('app.flask_ml_url', 'http://localhost:5000');
    }

    /**
     * Run prediction for a single bus and persist the result.
     */
    public function predictForBus(Bus $bus): ?FailurePrediction
    {
        $features = $this->buildFeatures($bus);

        try {
            $response = Http::timeout(10)->post("{$this->flaskUrl}/predict", $features);

            if (! $response->successful()) {
                Log::warning("Flask ML returned {$response->status()} for bus {$bus->id}");
                return null;
            }

            $data = $response->json();

            return FailurePrediction::create([
                'bus_id'                  => $bus->id,
                'risk_level'              => $data['risk_level'],
                'predicted_failure_type'  => $data['predicted_failure_type'],
                'confidence_score'        => $data['confidence_score'],
                'recommended_action'      => $data['recommended_action'],
                'feature_contributions'   => $data['feature_contributions'] ?? null,
                'predicted_at'            => now(),
            ]);
        } catch (\Exception $e) {
            Log::error("Prediction failed for bus {$bus->id}: {$e->getMessage()}");
            return null;
        }
    }

    private function buildFeatures(Bus $bus): array
    {
        $lastMaintenance = $bus->maintenanceRecords()->latest('maintenance_date')->first();
        $sensorAvg       = $bus->sensorReadings()
            ->where('recorded_at', '>=', now()->subDays(30))
            ->selectRaw('AVG(temperature) as avg_temp, AVG(vibration) as avg_vib, AVG(oil_pressure) as avg_pressure')
            ->first();

        $lastPM          = $bus->preventiveMaintenances()->latest('last_service_date')->first();
        $failureCount6m  = $bus->workOrders()
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subMonths(6))
            ->count();

        $lastFailure     = $bus->workOrders()
            ->where('status', 'completed')
            ->latest('completion_date')
            ->first();

        return [
            'bus_mileage'              => $bus->mileage ?? 0,
            'days_since_last_service'  => $lastMaintenance
                ? now()->diffInDays($lastMaintenance->maintenance_date)
                : 90,
            'avg_temperature_30d'      => $sensorAvg->avg_temp ?? 75.0,
            'avg_vibration_30d'        => $sensorAvg->avg_vib ?? 0.5,
            'avg_pressure_30d'         => $sensorAvg->avg_pressure ?? 45.0,
            'failure_count_6m'         => $failureCount6m,
            'days_since_last_failure'  => $lastFailure
                ? now()->diffInDays($lastFailure->completion_date)
                : 365,
            'service_interval_days'    => $lastPM->interval_days ?? 90,
        ];
    }
}
