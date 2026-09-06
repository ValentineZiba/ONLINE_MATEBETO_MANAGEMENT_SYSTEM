<?php

namespace App\Livewire\Public;

use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Polling;
use Livewire\Component;

#[Layout('components.layouts.public', ['title' => 'Track Your Order'])]
class OrderTracker extends Component
{
    public string $orderNumber = '';
    public ?Order $order = null;
    public string $error = '';
    public bool $tracking = false;

    public function mount(?Order $order = null): void
    {
        if ($order && $order->exists) {
            $this->order = $order->load('items.menuItem');
            $this->orderNumber = $order->order_number;
            $this->tracking = true;
        }
    }

    #[Polling('10s')]
    public function refresh(): void
    {
        if ($this->order) {
            $this->order = Order::find($this->order->id)?->load('items.menuItem');
        }
    }

    public function track(): void
    {
        $this->error = '';
        $this->order = null;

        if (empty($this->orderNumber)) {
            $this->error = 'Please enter an order number.';
            return;
        }

        $order = Order::where('order_number', strtoupper(trim($this->orderNumber)))
            ->with('items.menuItem')
            ->first();

        if (!$order) {
            $this->error = 'Order not found. Please check your order number.';
            return;
        }

        $this->order = $order;
        $this->tracking = true;
    }

    public function getProgressStepsProperty(): array
    {
        $steps = [
            ['key' => 'pending', 'label' => 'Order Received', 'icon' => '📋'],
            ['key' => 'confirmed', 'label' => 'Confirmed', 'icon' => '✅'],
            ['key' => 'preparing', 'label' => 'Being Prepared', 'icon' => '👨‍🍳'],
            ['key' => 'ready', 'label' => 'Ready', 'icon' => '🔔'],
            ['key' => 'served', 'label' => 'Served / Delivered', 'icon' => '🍽️'],
        ];

        $currentIndex = collect($steps)->search(fn ($s) => $s['key'] === $this->order?->status);

        return collect($steps)->map(fn ($step, $index) => array_merge($step, [
            'completed' => $currentIndex !== false && $index <= $currentIndex,
            'current' => $this->order?->status === $step['key'],
        ]))->all();
    }

    public function render()
    {
        return view('livewire.public.order-tracker');
    }
}
