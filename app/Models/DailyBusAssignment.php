<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyBusAssignment extends Model
{
    protected $fillable = [
        'bus_id', 'driver_id', 'assigned_date', 'assigned_by', 'status', 'notes', 'route',
    ];

    protected $casts = [
        'assigned_date' => 'date',
    ];

    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public static function todayFor(int $driverId): ?self
    {
        return self::with('bus')
            ->where('driver_id', $driverId)
            ->where('assigned_date', today())
            ->where('status', 'active')
            ->first();
    }
}
