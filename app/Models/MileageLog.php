<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MileageLog extends Model
{
    protected $fillable = [
        'bus_id', 'driver_id', 'trip_date',
        'km_traveled', 'odometer_reading', 'route', 'notes',
    ];

    protected $casts = [
        'trip_date' => 'date',
    ];

    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}
