<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryRider extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'phone', 'vehicle_type', 'plate_number',
        'rider_photo_path', 'nrc_front_path', 'nrc_back_path', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'delivery_rider_id');
    }

    public function requiresNrc(): bool
    {
        return $this->vehicle_type === 'bicycle';
    }

    public function requiresPlateNumber(): bool
    {
        return in_array($this->vehicle_type, ['car', 'motorbike'], true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getVehicleIconAttribute(): string
    {
        return match ($this->vehicle_type) {
            'car' => '🚗',
            'motorbike' => '🏍️',
            'bicycle' => '🚲',
            default => '🚚',
        };
    }
}
