<?php

namespace App\Console\Commands;

use App\Models\Bus;
use App\Services\PredictionService;
use Illuminate\Console\Command;

class RunPredictions extends Command
{
    protected $signature   = 'predictions:run-all';
    protected $description = 'Run AI failure predictions for all active buses';

    public function __construct(private PredictionService $predictionService)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $buses = Bus::where('status', 'active')->get();
        $success = 0;
        $failed  = 0;

        foreach ($buses as $bus) {
            $prediction = $this->predictionService->predictForBus($bus);
            $prediction ? $success++ : $failed++;
            $this->line("Bus {$bus->registration_number}: " . ($prediction ? "✓ {$prediction->risk_level}" : "✗ failed"));
        }

        $this->info("Predictions complete — {$success} succeeded, {$failed} failed.");
    }
}
