<?php

namespace App\Livewire\Public;

use App\Models\Order;
use App\Models\Review;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.public', ['title' => 'Leave a Review'])]
class ReviewForm extends Component
{
    public ?Order $order = null;
    public string $orderNumber = '';
    public int $rating = 5;
    public int $foodRating = 5;
    public int $serviceRating = 5;
    public int $ambienceRating = 5;
    public string $comment = '';
    public string $reviewerName = '';
    public bool $submitted = false;
    public string $error = '';

    public function mount(): void
    {
        if (auth()->check()) {
            $this->reviewerName = auth()->user()->name;
        }
    }

    public function lookupOrder(): void
    {
        $this->error = '';
        $order = Order::where('order_number', strtoupper(trim($this->orderNumber)))
            ->where('status', 'completed')
            ->first();

        if (!$order) {
            $this->error = 'No completed order found with that number.';
            return;
        }

        if ($order->review()->exists()) {
            $this->error = 'A review has already been submitted for this order.';
            return;
        }

        $this->order = $order;
    }

    public function submitReview(): void
    {
        $this->validate([
            'reviewerName' => 'required|string|min:2',
            'rating'        => 'required|integer|between:1,5',
            'foodRating'    => 'required|integer|between:1,5',
            'serviceRating' => 'required|integer|between:1,5',
            'ambienceRating'=> 'required|integer|between:1,5',
            'comment'       => 'required|string|min:10|max:1000',
        ]);

        Review::create([
            'order_id'       => $this->order->id,
            'user_id'        => auth()->id(),
            'reviewer_name'  => $this->reviewerName,
            'rating'         => $this->rating,
            'food_rating'    => $this->foodRating,
            'service_rating' => $this->serviceRating,
            'ambience_rating'=> $this->ambienceRating,
            'comment'        => $this->comment,
            'is_approved'    => false,
        ]);

        $this->submitted = true;
    }

    public function render()
    {
        return view('livewire.public.review-form');
    }
}
