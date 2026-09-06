<?php

namespace App\Models;

use App\Models\Concerns\HasPaymentTransactions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarTab extends Model
{
    use HasFactory, HasPaymentTransactions;

    protected $fillable = [
        'table_id', 'opened_by', 'customer_name', 'status', 'payment_status',
        'payment_method', 'subtotal', 'tax', 'total', 'notes', 'closed_at',
    ];

    protected $casts = ['closed_at' => 'datetime'];

    public function table() { return $this->belongsTo(RestaurantTable::class, 'table_id'); }
    public function opener() { return $this->belongsTo(User::class, 'opened_by'); }
    public function items() { return $this->hasMany(BarTabItem::class); }

    public function recalculate(): void
    {
        $subtotal = $this->items->sum('subtotal');
        $tax = round($subtotal * 0.16, 2);
        $this->update([
            'subtotal' => $subtotal,
            'tax'      => $tax,
            'total'    => $subtotal + $tax,
        ]);
    }

    public function scopeOpen($q) { return $q->where('status', 'open'); }
}
