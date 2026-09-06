<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Reservation extends Model
{
    protected $fillable = [
        'confirmation_code', 'user_id', 'table_id', 'guest_name', 'guest_email',
        'guest_phone', 'party_size', 'reservation_date', 'reservation_time',
        'status', 'special_requests', 'notes', 'occasion', 'reminded_at',
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'reminded_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($model) => $model->confirmation_code ??= strtoupper(Str::random(8)));
    }

    public function user() { return $this->belongsTo(User::class); }
    public function table() { return $this->belongsTo(RestaurantTable::class, 'table_id'); }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'confirmed' => 'blue',
            'seated' => 'green',
            'completed' => 'gray',
            'cancelled' => 'red',
            'no_show' => 'orange',
            default => 'gray',
        };
    }

    public function scopePending($query) { return $query->where('status', 'pending'); }
    public function scopeToday($query) { return $query->whereDate('reservation_date', today()); }
    public function scopeUpcoming($query) { return $query->whereDate('reservation_date', '>=', today()); }
}
