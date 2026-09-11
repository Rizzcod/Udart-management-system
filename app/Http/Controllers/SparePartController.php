<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\SparePart;
use Illuminate\Http\Request;

class SparePartController extends Controller
{
    public function index(Request $request)
    {
        $parts = SparePart::query()
            ->when($request->search, fn($q, $s) => $q->where('part_name', 'like', "%$s%")->orWhere('part_number', 'like', "%$s%"))
            ->when($request->low_stock, fn($q) => $q->whereColumn('quantity', '<=', 'minimum_stock'))
            ->withCount('usages')
            ->orderBy('part_name')
            ->paginate(20)
            ->withQueryString();

        return view('spare-parts.index', compact('parts'));
    }

    public function create()
    {
        return view('spare-parts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'part_name'     => 'required|string|max:200',
            'part_number'   => 'required|string|unique:spare_parts',
            'quantity'      => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'unit_price'    => 'required|numeric|min:0',
            'supplier'      => 'nullable|string|max:200',
            'notes'         => 'nullable|string',
        ]);

        $part = SparePart::create($data);

        if ($part->isLowStock()) {
            Notification::create([
                'title'   => "Low Stock: {$part->part_name}",
                'message' => "New part {$part->part_name} added but stock ({$part->quantity}) is at or below minimum ({$part->minimum_stock}).",
                'type'    => 'warning',
            ]);
        }

        return redirect()->route('spare-parts.index')->with('success', 'Spare part added successfully.');
    }

    public function show(SparePart $sparePart)
    {
        $sparePart->load(['usages.workOrder.bus']);
        return view('spare-parts.show', compact('sparePart'));
    }

    public function edit(SparePart $sparePart)
    {
        return view('spare-parts.edit', compact('sparePart'));
    }

    public function update(Request $request, SparePart $sparePart)
    {
        $data = $request->validate([
            'part_name'     => 'required|string|max:200',
            'part_number'   => 'required|string|unique:spare_parts,part_number,' . $sparePart->id,
            'quantity'      => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'unit_price'    => 'required|numeric|min:0',
            'supplier'      => 'nullable|string|max:200',
            'notes'         => 'nullable|string',
        ]);

        $sparePart->update($data);

        if ($sparePart->isLowStock()) {
            Notification::create([
                'title'   => "Low Stock: {$sparePart->part_name}",
                'message' => "Stock for {$sparePart->part_name} is at {$sparePart->quantity} — below minimum of {$sparePart->minimum_stock}.",
                'type'    => 'warning',
            ]);
        }

        return redirect()->route('spare-parts.index')->with('success', 'Spare part updated.');
    }

    public function destroy(SparePart $sparePart)
    {
        $sparePart->delete();
        return redirect()->route('spare-parts.index')->with('success', 'Spare part removed.');
    }
}
