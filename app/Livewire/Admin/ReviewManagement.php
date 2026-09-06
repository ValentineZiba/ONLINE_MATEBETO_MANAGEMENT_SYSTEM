<?php

namespace App\Livewire\Admin;

use App\Models\Review;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin', ['title' => 'Reviews'])]
class ReviewManagement extends Component
{
    use WithPagination;

    public string $filter = 'all';

    public function approve(int $id): void
    {
        Review::findOrFail($id)->update(['is_approved' => true]);
    }

    public function reject(int $id): void
    {
        Review::findOrFail($id)->update(['is_approved' => false]);
    }

    public function delete(int $id): void
    {
        Review::findOrFail($id)->delete();
    }

    public function render()
    {
        $reviews = Review::with(['user', 'menuItem', 'order'])
            ->when($this->filter === 'pending', fn ($q) => $q->where('is_approved', false))
            ->when($this->filter === 'approved', fn ($q) => $q->where('is_approved', true))
            ->latest()
            ->paginate(20);

        $stats = [
            'total' => Review::count(),
            'pending' => Review::where('is_approved', false)->count(),
            'approved' => Review::where('is_approved', true)->count(),
            'avg_rating' => round(Review::approved()->avg('rating'), 1),
        ];

        return view('livewire.admin.review-management', compact('reviews', 'stats'));
    }
}
