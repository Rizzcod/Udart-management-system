<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SensorReading extends Model
{
    protected $fillable = [
        'bus_id', 'temperature', 'oil_temperature', 'vibration',
        'oil_pressure', 'battery_voltage', 'recorded_at',
    ];

    protected $casts = [
        'recorded_at'     => 'datetime',
        'temperature'     => 'decimal:2',
        'oil_temperature' => 'decimal:2',
        'vibration'       => 'decimal:4',
        'oil_pressure'    => 'decimal:2',
        'battery_voltage' => 'decimal:2',
    ];

    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class);
    }

    public function getHealthStatus(): string
    {
        if (
            $this->temperature > 95 ||
            $this->oil_temperature > 130 ||
            $this->vibration > 2.5 ||
            $this->oil_pressure < 20 ||
            $this->battery_voltage < 11
        ) {
            return 'critical';
        }
        if (
            $this->temperature > 85 ||
            $this->oil_temperature > 115 ||
            $this->vibration > 1.5 ||
            $this->oil_pressure < 30 ||
            $this->battery_voltage < 12
        ) {
            return 'warning';
        }
        return 'normal';
    }

    /**
     * Return which specific sensors are in a non-normal state, with the severity per sensor.
     */
    public function getSensorAlerts(): array
    {
        $alerts = [];

        if ($this->temperature > 95) {
            $alerts[] = ['sensor' => 'Coolant Temperature', 'value' => $this->temperature.'°C', 'severity' => 'critical'];
        } elseif ($this->temperature > 85) {
            $alerts[] = ['sensor' => 'Coolant Temperature', 'value' => $this->temperature.'°C', 'severity' => 'warning'];
        }

        if ($this->oil_temperature > 130) {
            $alerts[] = ['sensor' => 'Oil Temperature', 'value' => $this->oil_temperature.'°C', 'severity' => 'critical'];
        } elseif ($this->oil_temperature > 115) {
            $alerts[] = ['sensor' => 'Oil Temperature', 'value' => $this->oil_temperature.'°C', 'severity' => 'warning'];
        }

        if ($this->vibration > 2.5) {
            $alerts[] = ['sensor' => 'Vibration', 'value' => $this->vibration.'g', 'severity' => 'critical'];
        } elseif ($this->vibration > 1.5) {
            $alerts[] = ['sensor' => 'Vibration', 'value' => $this->vibration.'g', 'severity' => 'warning'];
        }

        if ($this->oil_pressure < 20) {
            $alerts[] = ['sensor' => 'Oil Pressure', 'value' => $this->oil_pressure.' PSI', 'severity' => 'critical'];
        } elseif ($this->oil_pressure < 30) {
            $alerts[] = ['sensor' => 'Oil Pressure', 'value' => $this->oil_pressure.' PSI', 'severity' => 'warning'];
        }

        if ($this->battery_voltage < 11) {
            $alerts[] = ['sensor' => 'Battery Voltage', 'value' => $this->battery_voltage.'V', 'severity' => 'critical'];
        } elseif ($this->battery_voltage < 12) {
            $alerts[] = ['sensor' => 'Battery Voltage', 'value' => $this->battery_voltage.'V', 'severity' => 'warning'];
        }

        return $alerts;
    }
}
