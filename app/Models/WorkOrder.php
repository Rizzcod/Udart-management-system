<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkOrder extends Model
{
    protected $fillable = [
        'bus_id', 'reported_by', 'assigned_to', 'title', 'description',
        'location', 'priority', 'status', 'start_date', 'completion_date', 'notes',
    ];

    protected $casts = [
        'start_date'      => 'date',
        'completion_date' => 'date',
    ];

    // Ordered stages for timeline display
    const STAGES = [
        'reported'            => ['label' => 'Reported',           'icon' => 'bi-send',              'color' => 'danger'],
        'assessment'          => ['label' => 'Assessment',         'icon' => 'bi-clipboard2-pulse',  'color' => 'warning'],
        'pending_approval'    => ['label' => 'Pending Approval',   'icon' => 'bi-hourglass-split',   'color' => 'warning'],
        'assigned'            => ['label' => 'Technician Assigned','icon' => 'bi-person-check',      'color' => 'info'],
        'awaiting_parts'      => ['label' => 'Awaiting Parts',     'icon' => 'bi-box-seam',          'color' => 'secondary'],
        'in_progress'         => ['label' => 'In Progress',        'icon' => 'bi-wrench-adjustable', 'color' => 'primary'],
        'testing'             => ['label' => 'Testing',            'icon' => 'bi-speedometer2',      'color' => 'info'],
        'completed'           => ['label' => 'Completed',          'icon' => 'bi-check-circle',      'color' => 'success'],
        'returned_to_service' => ['label' => 'Returned to Service','icon' => 'bi-bus-front',         'color' => 'success'],
    ];

    // --- Relationships ---

    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function sparePartUsages(): HasMany
    {
        return $this->hasMany(SparePartUsage::class);
    }

    // --- Status helpers ---

    public function getStatusLabel(): string
    {
        return self::STAGES[$this->status]['label']
            ?? ucwords(str_replace('_', ' ', $this->status));
    }

    public function getPriorityBadgeClass(): string
    {
        return match ($this->priority) {
            'critical' => 'danger',
            'high'     => 'warning',
            'medium'   => 'info',
            default    => 'secondary',
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'completed', 'returned_to_service' => 'success',
            'in_progress', 'testing'           => 'primary',
            'assigned'                         => 'info',
            'reported', 'assessment',
            'pending_approval', 'pending'      => 'warning',
            'awaiting_parts'                   => 'secondary',
            'cancelled'                        => 'dark',
            default                            => 'secondary',
        };
    }

    public function isResolved(): bool
    {
        return in_array($this->status, ['completed', 'returned_to_service', 'cancelled']);
    }
}
