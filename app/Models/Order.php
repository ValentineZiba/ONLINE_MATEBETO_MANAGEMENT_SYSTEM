<?php

namespace App\Models;

use App\Mail\OrderConfirmationMail;
use App\Models\Concerns\HasPaymentTransactions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory, HasPaymentTransactions;

    protected $fillable = [
        'order_number', 'user_id', 'table_id', 'order_type', 'status',
        'customer_name', 'customer_email', 'customer_phone', 'delivery_address',
        'subtotal', 'tax', 'delivery_fee', 'discount', 'total',
        'coupon_code', 'payment_method', 'payment_status', 'payment_reference',
        'notes', 'estimated_minutes', 'accepted_at', 'ready_at', 'completed_at',
        'delivery_rider_id', 'driver_name', 'driver_phone', 'delivery_status', 'picked_up_at', 'delivered_at',
        'loyalty_points_earned', 'loyalty_points_redeemed',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'accepted_at' => 'datetime',
        'ready_at' => 'datetime',
        'completed_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'delivered_at' => 'datetime',
        'loyalty_points_earned' => 'integer',
        'loyalty_points_redeemed' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->order_number ??= 'ORD-' . strtoupper(Str::random(8));
        });
    }

    public function user() { return $this->belongsTo(User::class); }
    public function table() { return $this->belongsTo(RestaurantTable::class, 'table_id'); }
    public function items() { return $this->hasMany(OrderItem::class); }
    public function review() { return $this->hasOne(Review::class); }
    public function deliveryRider() { return $this->belongsTo(DeliveryRider::class); }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'confirmed' => 'blue',
            'preparing' => 'orange',
            'ready' => 'green',
            'served' => 'teal',
            'completed' => 'gray',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'preparing' => 'Preparing',
            'ready' => 'Ready',
            'served' => 'Served',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status),
        };
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['completed', 'cancelled']);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function recalculateTotals()
    {
        $subtotal = $this->items->sum('subtotal');
        $tax = $subtotal * 0.16;
        $this->update([
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $subtotal + $tax + $this->delivery_fee - $this->discount,
        ]);
    }

    /**
     * Fired once by PaymentManager the first time payment for this order
     * succeeds. Stock deduction, coupon usage, and loyalty redemption used
     * to happen unconditionally at order-creation time even if payment
     * never completed — moved here so they only happen once money has
     * actually been confirmed.
     */
    public function onPaymentSucceeded(PaymentTransaction $transaction): void
    {
        foreach ($this->items()->with('menuItem')->get() as $orderItem) {
            $menuItem = $orderItem->menuItem;
            if ($menuItem && $menuItem->stock_quantity !== null) {
                $newStock = max(0, $menuItem->stock_quantity - $orderItem->quantity);
                $menuItem->stock_quantity = $newStock;
                if ($newStock === 0) {
                    $menuItem->is_available = false;
                }
                $menuItem->save();
            }
        }

        if ($this->coupon_code) {
            Coupon::where('code', $this->coupon_code)->increment('uses_count');
        }

        if ($this->loyalty_points_redeemed > 0 && $this->user) {
            $this->user->redeemLoyaltyPoints($this->loyalty_points_redeemed);
        }

        if ($this->customer_email) {
            Mail::to($this->customer_email)->send(new OrderConfirmationMail($this->load('items.menuItem')));
        }
    }
}
