<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RestaurantTable extends Model
{
    use HasFactory;

    protected $table = 'restaurant_tables';

    protected $fillable = ['number', 'capacity', 'location', 'status', 'notes', 'position_x', 'position_y', 'qr_token'];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (self $table) {
            if (empty($table->qr_token)) {
                $table->qr_token = Str::uuid()->toString();
            }
        });
    }

    public function qrUrl(): string
    {
        return route('qr.order', $this->qr_token);
    }

    public function reservations() { return $this->hasMany(Reservation::class, 'table_id'); }
    public function orders() { return $this->hasMany(Order::class, 'table_id'); }

    public function activeOrder()
    {
        return $this->hasOne(Order::class, 'table_id')
            ->whereIn('status', ['confirmed', 'preparing', 'ready', 'served']);
    }

    public function scopeAvailable($query) { return $query->where('status', 'available'); }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'available' => 'green',
            'occupied' => 'red',
            'reserved' => 'yellow',
            'cleaning' => 'blue',
            'inactive' => 'gray',
            default => 'gray',
        };
    }
}
