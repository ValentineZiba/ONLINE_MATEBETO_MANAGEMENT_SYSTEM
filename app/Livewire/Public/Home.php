<?php

namespace App\Livewire\Public;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Review;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.public', ['title' => 'Welcome'])]
class Home extends Component
{
    public function render()
    {
        return view('livewire.public.home', [
            'featuredItems' => MenuItem::featured()->available()->with('category')->take(6)->get(),
            'categories' => Category::active()->orderBy('sort_order')->take(8)->get(),
            'reviews' => Review::approved()->with('user')->latest()->take(6)->get(),
        ]);
    }
}
