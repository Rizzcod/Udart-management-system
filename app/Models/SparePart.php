<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SparePart extends Model
{
    protected $fillable = [
        'part_name', 'part_number', 'quantity',
        'minimum_stock', 'unit_price', 'supplier', 'notes',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
    ];

    public function usages(): HasMany
    {
        return $this->hasMany(SparePartUsage::class);
    }

    public function isLowStock(): bool
    {
        return $this->quantity <= $this->minimum_stock;
    }
}
