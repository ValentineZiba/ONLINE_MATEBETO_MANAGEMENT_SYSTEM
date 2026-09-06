<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'price', 'discount_price',
        'image', 'images', 'is_available', 'is_featured', 'is_vegetarian', 'is_vegan',
        'is_gluten_free', 'is_spicy', 'preparation_time', 'calories',
        'allergens', 'modifiers', 'sort_order', 'stock_quantity', 'station',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'images' => 'array',
        'allergens' => 'array',
        'modifiers' => 'array',
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        'is_vegetarian' => 'boolean',
        'is_vegan' => 'boolean',
        'is_gluten_free' => 'boolean',
        'is_spicy' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($model) => $model->slug ??= Str::slug($model->name) . '-' . Str::random(4));
    }

    public function category() { return $this->belongsTo(Category::class); }
    public function orderItems() { return $this->hasMany(OrderItem::class); }
    public function reviews() { return $this->hasMany(Review::class); }

    public function getEffectivePriceAttribute(): float
    {
        return $this->discount_price ?? $this->price;
    }

    public function scopeAvailable($query) { return $query->where('is_available', true); }
    public function scopeFeatured($query) { return $query->where('is_featured', true); }
}
