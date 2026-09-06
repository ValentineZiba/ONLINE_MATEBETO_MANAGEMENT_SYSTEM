<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = [
        'name', 'unit', 'quantity', 'low_stock_threshold',
        'cost_per_unit', 'supplier', 'category', 'is_active', 'last_restocked_at',
    ];

    protected $casts = [
        'quantity'           => 'decimal:3',
        'low_stock_threshold'=> 'decimal:3',
        'cost_per_unit'      => 'decimal:2',
        'is_active'          => 'boolean',
        'last_restocked_at'  => 'datetime',
    ];

    public function getIsLowStockAttribute(): bool
    {
        return $this->quantity <= $this->low_stock_threshold;
    }

    public function scopeActive($query) { return $query->where('is_active', true); }
    public function scopeLowStock($query) { return $query->whereColumn('quantity', '<=', 'low_stock_threshold'); }
}
