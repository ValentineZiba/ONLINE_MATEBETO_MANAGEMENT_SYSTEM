<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarTabItem extends Model
{
    protected $fillable = ['bar_tab_id', 'menu_item_id', 'quantity', 'unit_price', 'subtotal', 'notes'];

    public function tab() { return $this->belongsTo(BarTab::class, 'bar_tab_id'); }
    public function menuItem() { return $this->belongsTo(MenuItem::class); }
}
