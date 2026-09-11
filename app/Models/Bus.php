<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bus extends Model
{
    protected $fillable = [
        'registration_number', 'model', 'manufacturer',
        'year', 'status', 'mileage', 'notes', 'driver_id',
        'maintenance_locked',
    ];

    protected $casts = [
        'maintenance_locked' => 'boolean',
    ];

    const STATUSES = [
        'active'                       => 'Active',
        'on_trip'                      => 'On Trip',
        'arrived'                      => 'Arrived at Terminal',
        'maintenance_due'              => 'Maintenance Due',
        'under_preventive_maintenance' => 'Under PM',
        'breakdown_reported'           => 'Breakdown Reported',
        'under_repair'                 => 'Under Repair',
        'awaiting_spare_parts'         => 'Awaiting Parts',
        'ready_for_service'            => 'Ready for Service',
        'out_of_service'               => 'Out of Service',
        'inactive'                     => 'Inactive',
    ];

    // Statuses that block dispatch
    const RESTRICTED_STATUSES = [
        'maintenance_due', 'under_preventive_maintenance',
        'breakdown_reported', 'under_repair',
        'awaiting_spare_parts', 'out_of_service', 'inactive',
    ];

    // --- Relationships ---

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class);
    }

    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class);
    }

    public function preventiveMaintenances(): HasMany
    {
        return $this->hasMany(PreventiveMaintenance::class);
    }

    public function sensorReadings(): HasMany
    {
        return $this->hasMany(SensorReading::class);
    }

    public function failurePredictions(): HasMany
    {
        return $this->hasMany(FailurePrediction::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function mileageLogs(): HasMany
    {
        return $this->hasMany(MileageLog::class);
    }

    public function dailyAssignments(): HasMany
    {
        return $this->hasMany(DailyBusAssignment::class);
    }

    // --- Convenience accessors ---

    public function latestPrediction(): ?FailurePrediction
    {
        return $this->failurePredictions()->latest('predicted_at')->first();
    }

    public function latestSensorReading(): ?SensorReading
    {
        return $this->sensorReadings()->latest('recorded_at')->first();
    }

    // --- Status helpers ---

    public function getStatusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucwords(str_replace('_', ' ', $this->status));
    }

    public function getStatusColors(): array
    {
        return match ($this->status) {
            'active'                       => ['bg' => '#dcfce7', 'text' => '#15803d'],
            'on_trip'                      => ['bg' => '#dbeafe', 'text' => '#1d4ed8'],
            'arrived'                      => ['bg' => '#fef9c3', 'text' => '#854d0e'],
            'maintenance_due'              => ['bg' => '#fef3c7', 'text' => '#92400e'],
            'under_preventive_maintenance' => ['bg' => '#e0e7ff', 'text' => '#3730a3'],
            'breakdown_reported'           => ['bg' => '#ffe4e6', 'text' => '#9f1239'],
            'under_repair'                 => ['bg' => '#ffedd5', 'text' => '#c2410c'],
            'awaiting_spare_parts'         => ['bg' => '#fef9c3', 'text' => '#854d0e'],
            'ready_for_service'            => ['bg' => '#d1fae5', 'text' => '#047857'],
            'out_of_service'               => ['bg' => '#fee2e2', 'text' => '#991b1b'],
            default                        => ['bg' => '#f3f4f6', 'text' => '#374151'],
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'active', 'ready_for_service'  => 'success',
            'on_trip'                      => 'primary',
            'arrived'                      => 'warning',
            'maintenance_due'              => 'warning',
            'under_preventive_maintenance' => 'info',
            'breakdown_reported'           => 'danger',
            'under_repair'                 => 'warning',
            'awaiting_spare_parts'         => 'secondary',
            'out_of_service'               => 'danger',
            default                        => 'secondary',
        };
    }

    public function isDispatchable(): bool
    {
        return in_array($this->status, ['active', 'arrived']) && ! $this->maintenance_locked;
    }

    public function isOperational(): bool
    {
        return in_array($this->status, ['active', 'on_trip']);
    }

    public function hasOverduePm(): bool
    {
        return $this->preventiveMaintenances()
            ->where('status', 'overdue')
            ->exists();
    }

    /** Put bus into scheduled PM. */
    public function lockForMaintenance(): void
    {
        $this->update([
            'status'             => 'under_preventive_maintenance',
            'maintenance_locked' => true,
        ]);
    }

    /** Return bus to active service after PM completes. */
    public function unlockFromMaintenance(): void
    {
        $this->update([
            'status'             => 'active',
            'maintenance_locked' => false,
        ]);
    }
}
