<?php

namespace App\Http\Controllers;

use App\Console\Commands\GeneratePmSchedules;
use App\Models\Bus;
use App\Models\Notification;
use App\Models\PreventiveMaintenance;
use App\Services\MaintenancePriorityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class PreventiveMaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $priorityService = new MaintenancePriorityService();

        $buses       = Bus::orderBy('registration_number')->get();
        $priorityMap = $priorityService->buildPriorityMap($buses);

        // Priority filter: compute matching bus IDs in PHP then apply as SQL IN clause
        $priorityBusIds = null;
        if ($request->priority) {
            $priorityBusIds = array_keys(array_filter(
                $priorityMap,
                fn($p) => $p['level'] === $request->priority
            ));
        }

        $schedules = PreventiveMaintenance::with('bus')
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->bus_id, fn($q, $b) => $q->where('bus_id', $b))
            ->when($priorityBusIds !== null, fn($q) => $q->whereIn('bus_id', $priorityBusIds))
            ->orderByRaw("FIELD(status,'overdue','upcoming','completed')")
            ->orderBy('next_service_date')
            ->paginate(20)
            ->withQueryString();

        // Buses flagged as critical or high priority for the AI recommendations panel
        $highPriorityBuses = collect($priorityMap)
            ->filter(fn($p) => in_array($p['level'], ['critical', 'high']))
            ->map(fn($p, $busId) => array_merge($p, ['bus' => $buses->firstWhere('id', $busId)]))
            ->sortByDesc('score')
            ->take(5)
            ->values();

        return view('preventive-maintenance.index', compact(
            'schedules', 'buses', 'priorityMap', 'highPriorityBuses'
        ));
    }

    public function create(Request $request)
    {
        $buses     = Bus::orderBy('registration_number')->get();
        $templates = config('pm_templates');

        $selectedBus = $request->bus_id
            ? $buses->firstWhere('id', $request->bus_id)
            : null;

        $priorityService = new MaintenancePriorityService();
        $busProfiles     = [];
        foreach ($buses as $bus) {
            $sd                    = $priorityService->scoreForBus($bus);
            $level                 = $priorityService->priorityLevel($sd['score']);
            $busProfiles[$bus->id] = [
                'score'      => $sd['score'],
                'level'      => $level,
                'multiplier' => $priorityService->intervalMultiplier($level),
                'insights'   => $priorityService->getInsights($bus, $sd),
                'bus_age'    => $sd['bus_age'],
                'wo_count'   => $sd['wo_count'],
            ];
        }

        return view('preventive-maintenance.create', compact(
            'buses', 'templates', 'selectedBus', 'busProfiles'
        ));
    }

    public function store(Request $request)
    {
        $templates    = config('pm_templates');
        $allowedTypes = array_keys($templates);

        $data = $request->validate([
            'bus_id'       => 'required|exists:buses,id',
            'service_type' => ['required', 'string', \Illuminate\Validation\Rule::in($allowedTypes)],
            'notes'        => 'nullable|string',
        ]);

        $exists = PreventiveMaintenance::where('bus_id', $data['bus_id'])
            ->where('service_type', $data['service_type'])
            ->whereIn('status', ['upcoming', 'overdue'])
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['service_type' => 'An active schedule for this service type already exists for this bus.'])
                ->withInput();
        }

        $template        = $templates[$data['service_type']];
        $bus             = Bus::findOrFail($data['bus_id']);
        $priorityService = new MaintenancePriorityService();
        $scoreData       = $priorityService->scoreForBus($bus);
        $level           = $priorityService->priorityLevel($scoreData['score']);
        $adjustedDays    = $priorityService->adjustedIntervalDays($bus, $template['interval_days']);

        PreventiveMaintenance::create([
            'bus_id'            => $data['bus_id'],
            'service_type'      => $data['service_type'],
            'interval_days'     => $adjustedDays,
            'interval_km'       => $template['interval_km'],
            'last_service_date' => null,
            'last_service_km'   => null,
            'next_service_date' => now()->addDays($adjustedDays)->toDateString(),
            'status'            => 'upcoming',
            'notes'             => $data['notes'] ?? null,
        ]);

        $msg = $level !== 'low'
            ? "PM schedule created. Interval adjusted to {$adjustedDays} days ({$level} priority vehicle). Standard is {$template['interval_days']} days."
            : 'PM schedule created and next service date calculated automatically.';

        return redirect()->route('preventive-maintenance.index')->with('success', $msg);
    }

    public function show(PreventiveMaintenance $preventiveMaintenance)
    {
        $preventiveMaintenance->load('bus');
        return view('preventive-maintenance.show', compact('preventiveMaintenance'));
    }

    public function edit(PreventiveMaintenance $preventiveMaintenance)
    {
        $preventiveMaintenance->load('bus');
        return view('preventive-maintenance.edit', compact('preventiveMaintenance'));
    }

    public function update(Request $request, PreventiveMaintenance $preventiveMaintenance)
    {
        $data = $request->validate([
            'status' => 'required|in:upcoming,overdue,completed',
            'notes'  => 'nullable|string',
        ]);

        $justCompleted = ($data['status'] === 'completed' && $preventiveMaintenance->status !== 'completed');

        if ($justCompleted) {
            $data['last_service_date'] = now()->toDateString();
        }

        $preventiveMaintenance->update($data);

        if ($justCompleted) {
            $template = config('pm_templates')[$preventiveMaintenance->service_type] ?? null;

            if ($template) {
                $bus             = $preventiveMaintenance->bus;
                $priorityService = new MaintenancePriorityService();
                $adjustedDays    = $priorityService->adjustedIntervalDays($bus, $template['interval_days']);

                PreventiveMaintenance::create([
                    'bus_id'            => $preventiveMaintenance->bus_id,
                    'service_type'      => $preventiveMaintenance->service_type,
                    'interval_days'     => $adjustedDays,
                    'interval_km'       => $template['interval_km'],
                    'last_service_date' => now()->toDateString(),
                    'last_service_km'   => $preventiveMaintenance->last_service_km,
                    'next_service_date' => now()->addDays($adjustedDays)->toDateString(),
                    'status'            => 'upcoming',
                ]);
            }

            // Unlock the bus if it has no more overdue PM
            $bus = $preventiveMaintenance->bus;
            $bus->refresh();

            if ($bus->maintenance_locked && ! $bus->hasOverduePm()) {
                $bus->unlockFromMaintenance();

                Notification::create([
                    'title'   => "Vehicle Cleared: {$bus->registration_number}",
                    'message' => "Bus {$bus->registration_number} has been restored to Active status. "
                        . "All overdue preventive maintenance has been completed. The vehicle is cleared to operate.",
                    'type'    => 'info',
                ]);
            }
        }

        $message = $justCompleted
            ? 'Service marked complete. Next cycle scheduled automatically.'
            : 'PM schedule updated.';

        return redirect()->route('preventive-maintenance.index')->with('success', $message);
    }

    public function destroy(PreventiveMaintenance $preventiveMaintenance)
    {
        $preventiveMaintenance->delete();
        return redirect()->route('preventive-maintenance.index')->with('success', 'PM schedule removed.');
    }

    public function bulkUpdateStatus(Request $request)
    {
        $data = $request->validate([
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'integer|exists:preventive_maintenance,id',
            'status' => 'required|in:upcoming,overdue,completed',
        ]);

        $schedules = PreventiveMaintenance::whereIn('id', $data['ids'])->get();
        $completed = 0;

        foreach ($schedules as $pm) {
            $justCompleted = ($data['status'] === 'completed' && $pm->status !== 'completed');

            $update = ['status' => $data['status']];
            if ($justCompleted) {
                $update['last_service_date'] = now()->toDateString();
            }

            $pm->update($update);

            if ($justCompleted) {
                $template = config('pm_templates')[$pm->service_type] ?? null;
                if ($template) {
                    $priorityService = new MaintenancePriorityService();
                    $adjustedDays    = $priorityService->adjustedIntervalDays($pm->bus, $template['interval_days']);

                    PreventiveMaintenance::create([
                        'bus_id'            => $pm->bus_id,
                        'service_type'      => $pm->service_type,
                        'interval_days'     => $adjustedDays,
                        'interval_km'       => $template['interval_km'],
                        'last_service_date' => now()->toDateString(),
                        'last_service_km'   => $pm->last_service_km,
                        'next_service_date' => now()->addDays($adjustedDays)->toDateString(),
                        'status'            => 'upcoming',
                    ]);
                }

                $bus = $pm->bus->refresh();
                if ($pm->bus->maintenance_locked && ! $pm->bus->hasOverduePm()) {
                    $pm->bus->unlockFromMaintenance();

                    Notification::create([
                        'title'   => "Vehicle Cleared: {$pm->bus->registration_number}",
                        'message' => "Bus {$pm->bus->registration_number} has been restored to Active status. "
                            . "All overdue preventive maintenance has been completed. The vehicle is cleared to operate.",
                        'type'    => 'info',
                    ]);
                }

                $completed++;
            }
        }

        $label   = ucfirst($data['status']);
        $count   = count($data['ids']);
        $message = $completed > 0
            ? "{$count} schedule(s) marked as {$label}. {$completed} next cycle(s) scheduled automatically."
            : "{$count} schedule(s) marked as {$label}.";

        return redirect()->route('preventive-maintenance.index')->with('success', $message);
    }

    public function generateAll()
    {
        Artisan::call(GeneratePmSchedules::class);
        $output = Artisan::output();

        preg_match('/(\d+) created, (\d+) advanced/', $output, $m);
        $created  = (int) ($m[1] ?? 0);
        $advanced = (int) ($m[2] ?? 0);

        return redirect()->route('preventive-maintenance.index')
            ->with('success', "Schedules auto-generated: {$created} new, {$advanced} advanced to next cycle.");
    }
}
