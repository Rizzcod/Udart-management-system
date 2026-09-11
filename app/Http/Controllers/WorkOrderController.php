<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\Notification;
use App\Models\SparePart;
use App\Models\SparePartUsage;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Http\Request;

class WorkOrderController extends Controller
{
    public function index(Request $request)
    {
        $workOrders = WorkOrder::with(['bus', 'assignee', 'reporter'])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->priority, fn($q, $p) => $q->where('priority', $p))
            ->when($request->bus_id, fn($q, $b) => $q->where('bus_id', $b))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $buses = Bus::orderBy('registration_number')->get();

        return view('work-orders.index', compact('workOrders', 'buses'));
    }

    public function create(Request $request)
    {
        $buses       = Bus::orderBy('registration_number')->get();
        $technicians = User::role('Technician')->orderBy('name')->get();
        $selectedBus = $request->bus_id ? Bus::find($request->bus_id) : null;

        // Pass restriction map so the view can warn without an extra AJAX request
        $restrictedBusIds = $buses->where('maintenance_locked', true)->pluck('id')->all();

        return view('work-orders.create', compact('buses', 'technicians', 'selectedBus', 'restrictedBusIds'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'bus_id'      => 'required|exists:buses,id',
            'title'       => 'required|string|max:200',
            'description' => 'required|string',
            'priority'    => 'required|in:low,medium,high,critical',
            'assigned_to' => 'nullable|exists:users,id',
            'start_date'  => 'nullable|date',
            'notes'       => 'nullable|string',
        ]);

        $data['reported_by'] = auth()->id();
        $data['status']      = $data['assigned_to'] ? 'assigned' : 'pending';

        $workOrder = WorkOrder::create($data);

        if ($workOrder->assigned_to) {
            Notification::create([
                'user_id' => $workOrder->assigned_to,
                'title'   => 'New Work Order Assigned',
                'message' => "You have been assigned work order: {$workOrder->title} on bus {$workOrder->bus->registration_number}.",
                'type'    => 'info',
            ]);
        }

        return redirect()->route('work-orders.show', $workOrder)->with('success', 'Work order created.');
    }

    public function myAssignments(Request $request)
    {
        $workOrders = WorkOrder::with(['bus', 'reporter'])
            ->where('assigned_to', auth()->id())
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->priority, fn($q, $p) => $q->where('priority', $p))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('work-orders.my-assignments', compact('workOrders'));
    }

    public function show(WorkOrder $workOrder)
    {
        $workOrder->load(['bus', 'reporter', 'assignee', 'sparePartUsages.sparePart']);
        $spareParts = SparePart::orderBy('part_name')->get();
        $technicians = User::role('Technician')->orderBy('name')->get();

        return view('work-orders.show', compact('workOrder', 'spareParts', 'technicians'));
    }

    public function edit(WorkOrder $workOrder)
    {
        $buses       = Bus::orderBy('registration_number')->get();
        $technicians = User::role('Technician')->orderBy('name')->get();

        return view('work-orders.edit', compact('workOrder', 'buses', 'technicians'));
    }

    public function update(Request $request, WorkOrder $workOrder)
    {
        $data = $request->validate([
            'bus_id'      => 'required|exists:buses,id',
            'title'       => 'required|string|max:200',
            'description' => 'required|string',
            'priority'    => 'required|in:low,medium,high,critical',
            'notes'       => 'nullable|string',
        ]);

        $workOrder->update($data);

        return redirect()->route('work-orders.show', $workOrder)->with('success', 'Work order updated.');
    }

    public function updateStatus(Request $request, WorkOrder $workOrder)
    {
        $allStatuses = ['reported', 'pending', 'assessment', 'pending_approval', 'assigned', 'awaiting_parts', 'in_progress', 'testing', 'completed', 'returned_to_service', 'cancelled'];
        $request->validate(['status' => 'required|in:' . implode(',', $allStatuses)]);

        $transitions = [
            'reported'            => ['assessment', 'cancelled'],
            'pending'             => ['assessment', 'assigned', 'cancelled'],
            'assessment'          => ['pending_approval', 'assigned', 'cancelled'],
            'pending_approval'    => ['assigned', 'cancelled'],
            'assigned'            => ['awaiting_parts', 'in_progress', 'cancelled'],
            'awaiting_parts'      => ['in_progress', 'cancelled'],
            'in_progress'         => ['testing', 'completed', 'cancelled'],
            'testing'             => ['completed', 'in_progress'],
            'completed'           => ['returned_to_service'],
            'returned_to_service' => [],
            'cancelled'           => [],
        ];

        $allowed = $transitions[$workOrder->status] ?? [];
        if (! in_array($request->status, $allowed)) {
            return back()->with('error', "Cannot transition from '{$workOrder->status}' to '{$request->status}'.");
        }

        $updateData = ['status' => $request->status];
        $bus        = $workOrder->bus;

        // Bus status side-effects
        match ($request->status) {
            'assessment'          => $bus->update(['status' => 'breakdown_reported']),
            'assigned'            => $bus->update(['status' => 'under_repair']),
            'awaiting_parts'      => $bus->update(['status' => 'awaiting_spare_parts']),
            'in_progress'         => $bus->update(['status' => 'under_repair']),
            'returned_to_service' => $bus->update(['status' => 'active', 'maintenance_locked' => false]),
            'cancelled'           => in_array($bus->status, ['breakdown_reported', 'under_repair', 'awaiting_spare_parts'])
                                        ? $bus->update(['status' => 'active', 'maintenance_locked' => false])
                                        : null,
            default               => null,
        };

        // Date tracking
        if ($request->status === 'in_progress' && ! $workOrder->start_date) {
            $updateData['start_date'] = now()->toDateString();
        }
        if (in_array($request->status, ['completed', 'returned_to_service'])) {
            $updateData['completion_date'] = now()->toDateString();
        }

        // Technician assignment
        if ($request->filled('assigned_to') && $request->status === 'assigned') {
            $updateData['assigned_to'] = $request->assigned_to;

            Notification::create([
                'user_id' => $request->assigned_to,
                'title'   => 'Work Order Assigned',
                'message' => "Work order '{$workOrder->title}' on bus {$bus->registration_number} has been assigned to you.",
                'type'    => 'info',
            ]);

            $reporter = $workOrder->reporter;
            if ($reporter && $reporter->hasRole('Driver')) {
                $techName = \App\Models\User::find($request->assigned_to)?->name ?? 'a technician';
                Notification::create([
                    'user_id' => $reporter->id,
                    'title'   => 'Technician Assigned to Your Report',
                    'message' => "{$techName} has been assigned to your breakdown report: {$workOrder->title}.",
                    'type'    => 'info',
                ]);
            }
        }

        // Spare parts on completion
        if (in_array($request->status, ['completed', 'returned_to_service']) && $request->filled('spare_part_id')) {
            foreach ($request->spare_part_id as $i => $partId) {
                $qty  = (int) ($request->quantity_used[$i] ?? 1);
                $part = SparePart::find($partId);
                if ($part && $part->quantity >= $qty) {
                    SparePartUsage::create([
                        'work_order_id' => $workOrder->id,
                        'spare_part_id' => $partId,
                        'quantity_used' => $qty,
                    ]);
                    $part->decrement('quantity', $qty);

                    if ($part->isLowStock()) {
                        Notification::create([
                            'title'   => "Low Stock: {$part->part_name}",
                            'message' => "Stock for {$part->part_name} ({$part->part_number}) is at {$part->quantity} units — below minimum of {$part->minimum_stock}.",
                            'type'    => 'warning',
                        ]);
                    }
                }
            }
        }

        // Final mileage
        if ($request->filled('final_mileage') && in_array($request->status, ['completed', 'returned_to_service'])) {
            $bus->update(['mileage' => $request->final_mileage]);
        }

        // Notify driver on return to service
        if (in_array($request->status, ['completed', 'returned_to_service'])) {
            $reporter = $workOrder->reporter;
            if ($reporter && $reporter->hasRole('Driver')) {
                Notification::create([
                    'user_id' => $reporter->id,
                    'title'   => 'Bus Repair Completed',
                    'message' => "The repair for '{$workOrder->title}' on bus {$bus->registration_number} has been completed. Your bus will be returned to service.",
                    'type'    => 'info',
                ]);
            }
        }

        $workOrder->update($updateData);

        return back()->with('success', 'Status updated to: ' . ucwords(str_replace('_', ' ', $request->status)) . '.');
    }

    public function destroy(WorkOrder $workOrder)
    {
        $workOrder->delete();
        return redirect()->route('work-orders.index')->with('success', 'Work order deleted.');
    }
}
