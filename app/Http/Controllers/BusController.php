<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\User;
use Illuminate\Http\Request;

class BusController extends Controller
{
    public function index(Request $request)
    {
        $buses = Bus::query()
            ->when($request->search, fn($q, $s) => $q->where('registration_number', 'like', "%$s%")->orWhere('model', 'like', "%$s%"))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->withCount(['workOrders', 'maintenanceRecords'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('buses.index', compact('buses'));
    }

    public function create()
    {
        $drivers = User::role('Driver')->orderBy('name')->get();
        return view('buses.create', compact('drivers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'registration_number' => 'required|string|unique:buses',
            'model'               => 'required|string|max:100',
            'manufacturer'        => 'required|string|max:100',
            'year'                => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'status'              => 'required|in:active,inactive,under_repair',
            'mileage'             => 'required|integer|min:0',
            'notes'               => 'nullable|string',
            'driver_id'           => 'nullable|exists:users,id',
        ]);

        Bus::create($data);

        return redirect()->route('buses.index')->with('success', 'Bus registered successfully.');
    }

    public function show(Bus $bus)
    {
        $bus->load(['maintenanceRecords.technician', 'workOrders.assignee', 'preventiveMaintenances']);
        $latestReading    = $bus->latestSensorReading();
        $latestPrediction = $bus->latestPrediction();

        return view('buses.show', compact('bus', 'latestReading', 'latestPrediction'));
    }

    public function edit(Bus $bus)
    {
        $drivers = User::role('Driver')->orderBy('name')->get();
        return view('buses.edit', compact('bus', 'drivers'));
    }

    public function update(Request $request, Bus $bus)
    {
        $data = $request->validate([
            'registration_number' => 'required|string|unique:buses,registration_number,' . $bus->id,
            'model'               => 'required|string|max:100',
            'manufacturer'        => 'required|string|max:100',
            'year'                => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'status'              => 'required|in:active,inactive,under_repair,out_of_service',
            'mileage'             => 'required|integer|min:0',
            'notes'               => 'nullable|string',
            'driver_id'           => 'nullable|exists:users,id',
        ]);

        // Prevent overriding the PM system's restriction
        if ($bus->maintenance_locked && $data['status'] === 'active') {
            return back()
                ->withErrors(['status' => 'Cannot set this bus to Active while it has overdue preventive maintenance. Complete all overdue PM tasks first.'])
                ->withInput();
        }

        // Prevent assigning a driver to a maintenance-locked bus
        if ($bus->maintenance_locked && ! empty($data['driver_id'])) {
            return back()
                ->withErrors(['driver_id' => 'Cannot assign a driver to this vehicle. It is restricted from operations due to overdue maintenance.'])
                ->withInput();
        }

        // Non-operational statuses must not have a driver assigned
        $nonOperational = ['out_of_service', 'inactive'];
        $driverCleared  = false;
        if (in_array($data['status'], $nonOperational)) {
            if (! empty($data['driver_id'])) {
                $driverCleared = true;
            }
            $data['driver_id'] = null;
        }

        $bus->update($data);

        $message = $driverCleared
            ? 'Bus updated. Driver was automatically unassigned because the bus is ' . ucwords(str_replace('_', ' ', $data['status'])) . '.'
            : 'Bus updated successfully.';

        return redirect()->route('buses.show', $bus)->with('success', $message);
    }

    public function destroy(Bus $bus)
    {
        $bus->delete();
        return redirect()->route('buses.index')->with('success', 'Bus removed.');
    }
}
