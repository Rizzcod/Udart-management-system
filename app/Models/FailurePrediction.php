<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FailurePrediction extends Model
{
    protected $fillable = [
        'bus_id', 'risk_level', 'predicted_failure_type',
        'confidence_score', 'recommended_action', 'predicted_at',
        'feature_contributions',
    ];

    protected $casts = [
        'predicted_at'          => 'datetime',
        'confidence_score'      => 'decimal:2',
        'feature_contributions' => 'array',
    ];

    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class);
    }

    public function getRiskBadgeClass(): string
    {
        return match($this->risk_level) {
            'high'   => 'danger',
            'medium' => 'warning',
            default  => 'success',
        };
    }
}
