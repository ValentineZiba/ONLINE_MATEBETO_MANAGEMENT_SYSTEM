<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = [
        'user_id', 'date', 'start_time', 'end_time', 'role', 'status', 'notes',
    ];

    protected $casts = ['date' => 'date'];

    public function user() { return $this->belongsTo(User::class); }

    public function getDurationHoursAttribute(): float
    {
        $start = \Carbon\Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->start_time);
        $end   = \Carbon\Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->end_time);
        return round($start->diffInMinutes($end) / 60, 1);
    }
}
