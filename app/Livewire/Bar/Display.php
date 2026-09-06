<?php

namespace App\Livewire\Bar;

use App\Models\Order;
use App\Models\OrderItem;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Polling;
use Livewire\Component;

#[Layout('components.layouts.bar', ['title' => 'Bar Display'])]
class Display extends Component
{
    public function updateOrderStatus(int $orderId, string $status): void
    {
        $order = Order::findOrFail($orderId);
        $updates = ['status' => $status];
        if ($status === 'preparing') $updates['accepted_at'] = now();
        if ($status === 'ready')     $updates['ready_at']    = now();
        $order->update($updates);
    }

    public function updateItemStatus(int $itemId, string $status): void
    {
        OrderItem::findOrFail($itemId)->update(['status' => $status]);
    }

    public function markAllBarItemsReady(int $orderId): void
    {
        $order = Order::findOrFail($orderId);

        // Only mark bar/both items ready
        $order->items()
            ->whereHas('menuItem', fn ($q) => $q->whereIn('station', ['bar', 'both']))
            ->update(['status' => 'ready']);

        // If ALL items on the order are now ready, promote the order
        $pendingItems = $order->items()->where('status', '!=', 'ready')->count();
        if ($pendingItems === 0) {
            $order->update(['status' => 'ready', 'ready_at' => now()]);
        }
    }

    #[Polling('15s')]
    public function render()
    {
        // Orders that have at least one bar/both item AND are still active
        $orders = Order::whereIn('status', ['pending', 'confirmed', 'preparing', 'ready'])
            ->whereHas('items.menuItem', fn ($q) => $q->whereIn('station', ['bar', 'both']))
            ->with(['items' => fn ($q) => $q->with('menuItem')
                ->whereHas('menuItem', fn ($q2) => $q2->whereIn('station', ['bar', 'both'])),
                'table',
            ])
            ->orderBy('created_at')
            ->get();

        $stats = [
            'pending'   => $orders->whereIn('status', ['pending', 'confirmed'])->count(),
            'preparing' => $orders->where('status', 'preparing')->count(),
            'ready'     => $orders->where('status', 'ready')->count(),
        ];

        return view('livewire.bar.display', compact('orders', 'stats'));
    }
}
