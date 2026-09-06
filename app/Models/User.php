<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'password', 'role', 'avatar', 'is_active',
        'loyalty_points', 'total_points_earned',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isManager(): bool { return in_array($this->role, ['admin', 'manager']); }
    public function isKitchen(): bool { return $this->role === 'kitchen'; }
    public function isWaiter(): bool { return $this->role === 'waiter'; }
    public function isBar(): bool { return $this->role === 'bar'; }
    public function isStaff(): bool { return in_array($this->role, ['admin', 'manager', 'kitchen', 'waiter', 'bar']); }

    public function orders() { return $this->hasMany(Order::class); }
    public function reservations() { return $this->hasMany(Reservation::class); }
    public function reviews() { return $this->hasMany(Review::class); }
    public function shifts() { return $this->hasMany(Shift::class); }

    public function addLoyaltyPoints(int $points): void
    {
        $this->increment('loyalty_points', $points);
        $this->increment('total_points_earned', $points);
    }

    public function redeemLoyaltyPoints(int $points): bool
    {
        if ($this->loyalty_points < $points) return false;
        $this->decrement('loyalty_points', $points);
        return true;
    }
}
