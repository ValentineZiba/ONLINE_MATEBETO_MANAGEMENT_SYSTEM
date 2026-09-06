<?php

namespace App\Livewire\Kitchen;

use App\Models\Order;
use App\Models\OrderItem;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Polling;
use Livewire\Component;

#[Layout('components.layouts.kitchen')]
class Display extends Component
{
    public string $view = 'board';

    public function updateOrderStatus(int $orderId, string $status): void
    {
        $order = Order::findOrFail($orderId);
        $updates = ['status' => $status];
        if ($status === 'confirmed') $updates['accepted_at'] = now();
        if ($status === 'ready') $updates['ready_at'] = now();
        $order->update($updates);
    }

    public function updateItemStatus(int $itemId, string $status): void
    {
        OrderItem::findOrFail($itemId)->update(['status' => $status]);
    }

    public function markAllItemsReady(int $orderId): void
    {
        OrderItem::where('order_id', $orderId)->update(['status' => 'ready']);
        Order::findOrFail($orderId)->update(['status' => 'ready', 'ready_at' => now()]);
    }

    #[Polling('15s')]
    public function render()
    {
        // Only orders that have at least one kitchen/both item (exclude pure-bar orders)
        $orders = Order::whereIn('status', ['pending', 'confirmed', 'preparing', 'ready'])
            ->whereHas('items.menuItem', fn ($q) => $q->whereIn('station', ['kitchen', 'both']))
            ->with(['items' => fn ($q) => $q->with('menuItem')
                ->whereHas('menuItem', fn ($q2) => $q2->whereIn('station', ['kitchen', 'both'])),
                'table',
            ])
            ->orderBy('created_at')
            ->get();

        $grouped = [
            'pending' => $orders->where('status', 'pending')->values(),
            'confirmed' => $orders->where('status', 'confirmed')->values(),
            'preparing' => $orders->where('status', 'preparing')->values(),
            'ready' => $orders->where('status', 'ready')->values(),
        ];

        $stats = [
            'total_active' => $orders->count(),
            'pending' => $grouped['pending']->count(),
            'preparing' => $grouped['preparing']->count() + $grouped['confirmed']->count(),
            'ready' => $grouped['ready']->count(),
        ];

        return view('livewire.kitchen.display', compact('grouped', 'stats'));
    }
}
