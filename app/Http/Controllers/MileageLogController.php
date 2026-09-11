<?php

namespace App\Http\Controllers;

use App\Models\DailyBusAssignment;
use App\Models\MileageLog;
use App\Models\Notification;
use Illuminate\Http\Request;

class MileageLogController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'trip_date'        => 'required|date|before_or_equal:today',
            'km_traveled'      => 'required|integer|min:1',
            'odometer_reading' => 'required|integer|min:0',
            'route'            => 'nullable|string|max:255',
            'notes'            => 'nullable|string',
        ]);

        $assignment = DailyBusAssignment::todayFor(auth()->id());
        abort_if(! $assignment, 403, 'You have no bus assigned for today.');
        $bus = $assignment->bus;

        if ($data['odometer_reading'] < $bus->mileage) {
            return back()
                ->withErrors(['odometer_reading' => 'Odometer cannot be less than current reading (' . number_format($bus->mileage) . ' km).'])
                ->withInput();
        }

        $exists = MileageLog::where('bus_id', $bus->id)
            ->where('trip_date', $data['trip_date'])
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['trip_date' => 'A mileage log for bus ' . $bus->registration_number . ' on ' . $data['trip_date'] . ' already exists.'])
                ->withInput();
        }

        $data['bus_id']    = $bus->id;
        $data['driver_id'] = auth()->id();

        MileageLog::create($data);
        $bus->update(['mileage' => $data['odometer_reading']]);
        $bus->refresh();

        $this->checkPmThresholds($bus);

        return back()->with('success', 'Trip logged: ' . number_format($data['km_traveled']) . ' km on ' . $data['trip_date'] . '.');
    }

    private function checkPmThresholds(\App\Models\Bus $bus): void
    {
        $anyNewOverdue = false;

        foreach ($bus->preventiveMaintenances()->where('status', 'upcoming')->whereNotNull('interval_km')->get() as $pm) {
            $kmSince = $bus->mileage - ($pm->last_service_km ?? 0);
            if ($kmSince >= $pm->interval_km) {
                $pm->update(['status' => 'overdue']);
                $anyNewOverdue = true;
            }
        }

        $hasOverdue = $bus->preventiveMaintenances()->where('status', 'overdue')->exists();

        if ($hasOverdue && ! in_array($bus->status, [
            'breakdown_reported', 'under_repair', 'awaiting_spare_parts',
            'under_preventive_maintenance', 'out_of_service',
        ])) {
            $bus->update(['status' => 'maintenance_due', 'maintenance_locked' => true]);

            if ($anyNewOverdue) {
                Notification::create([
                    'title'   => "PM Due: Bus {$bus->registration_number}",
                    'message' => "Bus {$bus->registration_number} has reached its preventive maintenance interval and has been restricted from dispatch. Schedule maintenance immediately.",
                    'type'    => 'warning',
                ]);
            }
        }
    }
}
