<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'user_id', 'order_id', 'menu_item_id', 'reviewer_name', 'rating',
        'food_rating', 'service_rating', 'ambience_rating', 'comment', 'is_approved',
    ];

    protected $casts = ['is_approved' => 'boolean'];

    public function user() { return $this->belongsTo(User::class); }
    public function order() { return $this->belongsTo(Order::class); }
    public function menuItem() { return $this->belongsTo(MenuItem::class); }

    public function scopeApproved($query) { return $query->where('is_approved', true); }
}
