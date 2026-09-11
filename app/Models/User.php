<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name', 'email', 'password',
        'phone', 'employee_id', 'department', 'position', 'last_login_at',
        'otp', 'otp_expires_at', 'email_verified_at',
    ];

    protected $hidden = [
        'password', 'remember_token', 'otp',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'otp_expires_at'    => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_verified_at);
    }

    public function otpStatus(): string
    {
        if ($this->email_verified_at)                          return 'active';
        if (!$this->otp)                                       return 'inactive';
        if ($this->otp_expires_at && $this->otp_expires_at->isPast()) return 'expired';
        return 'pending';
    }

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class, 'assigned_to');
    }

    public function reportedWorkOrders()
    {
        return $this->hasMany(WorkOrder::class, 'reported_by');
    }

    public function maintenanceRecords()
    {
        return $this->hasMany(MaintenanceRecord::class, 'technician_id');
    }

    public function assignedBus()
    {
        return $this->hasOne(Bus::class, 'driver_id');
    }

    public function initials(): string
    {
        $words = explode(' ', trim($this->name));
        return strtoupper(
            count($words) >= 2
                ? $words[0][0] . $words[1][0]
                : substr($this->name, 0, 2)
        );
    }
}
