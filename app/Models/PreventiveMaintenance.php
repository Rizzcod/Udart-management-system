<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreventiveMaintenance extends Model
{
    protected $table = 'preventive_maintenance';

    protected $fillable = [
        'bus_id', 'service_type', 'interval_days', 'interval_km',
        'last_service_date', 'last_service_km', 'next_service_date',
        'status', 'notes',
    ];

    protected $casts = [
        'last_service_date' => 'date',
        'next_service_date' => 'date',
    ];

    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class);
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'overdue'   => 'danger',
            'upcoming'  => 'warning',
            'completed' => 'success',
            default     => 'secondary',
        };
    }
}
