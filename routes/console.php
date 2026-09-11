<?php

use App\Console\Commands\AssignBusesToDrivers;
use App\Console\Commands\CheckPreventiveMaintenance;
use App\Console\Commands\CheckPmThresholds;
use App\Console\Commands\EndOfService;
use App\Console\Commands\EnforceVehicleRestrictions;
use App\Console\Commands\GeneratePmSchedules;
use App\Console\Commands\RunPredictions;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── End of service (midnight) ────────────────────────────────────────────────
// At 00:01 AM, return all on-trip/arrived buses to depot and auto-close every
// active assignment from the previous day (or any older stale day).
// Runs at 00:01 rather than 00:00 to ensure date rollover is settled first.
Schedule::command(EndOfService::class)->dailyAt('00:01');

// ── Morning maintenance pipeline ─────────────────────────────────────────────
// 05:45 — auto-generate missing PM schedules and advance completed cycles
Schedule::command(GeneratePmSchedules::class)->dailyAt('05:45');
// 05:55 — check mileage-based PM thresholds and flag overdue buses
Schedule::command(CheckPmThresholds::class)->dailyAt('05:55');
// 06:00 — mark any past-due PM records as overdue
Schedule::command(CheckPreventiveMaintenance::class)->dailyAt('06:00');
// 06:05 — lock/unlock buses based on overdue PM; send 7-day and 3-day advance warnings
Schedule::command(EnforceVehicleRestrictions::class)->dailyAt('06:05');

// ── Auto-assignment (04:30) ───────────────────────────────────────────────────
// Assign dispatchable buses to drivers early so drivers see their assignment on login at 05:00
Schedule::command(AssignBusesToDrivers::class)->dailyAt('04:30');

// ── AI predictions (06:30) ───────────────────────────────────────────────────
Schedule::command(RunPredictions::class)->dailyAt('06:30');
