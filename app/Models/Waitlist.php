<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Waitlist extends Model
{
    protected $table = 'waitlist';

    protected $fillable = [
        'guest_name', 'phone', 'email', 'party_size',
        'status', 'notes', 'notified_at', 'seated_at',
    ];

    protected $casts = [
        'notified_at' => 'datetime',
        'seated_at'   => 'datetime',
    ];

    public function scopeActive($query) { return $query->whereIn('status', ['waiting', 'notified']); }

    public function getWaitMinutesAttribute(): int
    {
        return (int) $this->created_at->diffInMinutes(now());
    }
}
