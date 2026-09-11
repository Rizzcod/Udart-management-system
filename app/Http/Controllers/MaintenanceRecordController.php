<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\MaintenanceRecord;
use App\Models\User;
use Illuminate\Http\Request;

class MaintenanceRecordController extends Controller
{
    public function index(Request $request)
    {
        $records = MaintenanceRecord::with(['bus', 'technician'])
            ->when($request->bus_id, fn($q, $b) => $q->where('bus_id', $b))
            ->latest('maintenance_date')
            ->paginate(20)
            ->withQueryString();

        $buses = Bus::orderBy('registration_number')->get();

        return view('maintenance-records.index', compact('records', 'buses'));
    }

    public function create(Request $request)
    {
        $buses       = Bus::orderBy('registration_number')->get();
        $technicians = User::role('Technician')->orderBy('name')->get();
        $selectedBus = $request->bus_id ? Bus::find($request->bus_id) : null;

        return view('maintenance-records.create', compact('buses', 'technicians', 'selectedBus'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'bus_id'           => 'required|exists:buses,id',
            'technician_id'    => 'required|exists:users,id',
            'description'      => 'required|string',
            'maintenance_date' => 'required|date',
            'parts_replaced'   => 'nullable|string',
            'notes'            => 'nullable|string',
        ]);

        MaintenanceRecord::create($data);

        return redirect()->route('maintenance-records.index')->with('success', 'Maintenance record added.');
    }

    public function show(MaintenanceRecord $maintenanceRecord)
    {
        $maintenanceRecord->load(['bus', 'technician']);
        return view('maintenance-records.show', compact('maintenanceRecord'));
    }

    public function edit(MaintenanceRecord $maintenanceRecord)
    {
        $buses       = Bus::orderBy('registration_number')->get();
        $technicians = User::role('Technician')->orderBy('name')->get();
        return view('maintenance-records.edit', compact('maintenanceRecord', 'buses', 'technicians'));
    }

    public function update(Request $request, MaintenanceRecord $maintenanceRecord)
    {
        $data = $request->validate([
            'bus_id'           => 'required|exists:buses,id',
            'technician_id'    => 'required|exists:users,id',
            'description'      => 'required|string',
            'maintenance_date' => 'required|date',
            'parts_replaced'   => 'nullable|string',
            'notes'            => 'nullable|string',
        ]);

        $maintenanceRecord->update($data);
        return redirect()->route('maintenance-records.index')->with('success', 'Maintenance record updated.');
    }

    public function destroy(MaintenanceRecord $maintenanceRecord)
    {
        $maintenanceRecord->delete();
        return redirect()->route('maintenance-records.index')->with('success', 'Record deleted.');
    }
}
