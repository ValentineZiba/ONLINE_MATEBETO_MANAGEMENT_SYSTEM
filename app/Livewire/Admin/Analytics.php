<?php

namespace App\Livewire\Admin;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Reservation;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.admin', ['title' => 'Analytics'])]
class Analytics extends Component
{
    #[Url] public string $period = '30';

    public function render()
    {
        $days = (int) $this->period;
        $start = now()->subDays($days)->startOfDay();

        $orders = Order::where('created_at', '>=', $start)->get();
        $paidOrders = $orders->where('payment_status', 'paid');

        $stats = [
            'total_revenue' => $paidOrders->sum('total'),
            'total_orders' => $orders->count(),
            'avg_order_value' => $paidOrders->count() > 0 ? $paidOrders->avg('total') : 0,
            'new_customers' => User::where('role', 'customer')->where('created_at', '>=', $start)->count(),
            'total_reservations' => Reservation::where('created_at', '>=', $start)->count(),
            'completion_rate' => $orders->count() > 0 ? round(($orders->where('status', 'completed')->count() / $orders->count()) * 100) : 0,
        ];

        $revenueByDay = collect(range($days - 1, 0))->map(fn ($d) => [
            'date' => now()->subDays($d)->format('M d'),
            'revenue' => Order::whereDate('created_at', now()->subDays($d))->where('payment_status', 'paid')->sum('total'),
            'orders' => Order::whereDate('created_at', now()->subDays($d))->count(),
        ]);

        $ordersByType = [
            'dine_in' => $orders->where('order_type', 'dine_in')->count(),
            'takeaway' => $orders->where('order_type', 'takeaway')->count(),
            'delivery' => $orders->where('order_type', 'delivery')->count(),
        ];

        $ordersByStatus = $orders->groupBy('status')->map->count();

        $topItems = OrderItem::whereHas('order', fn ($q) => $q->where('created_at', '>=', $start))
            ->selectRaw('menu_item_id, SUM(quantity) as total_qty, SUM(subtotal) as total_revenue')
            ->groupBy('menu_item_id')
            ->orderByDesc('total_qty')
            ->with('menuItem:id,name,price')
            ->take(10)
            ->get();

        $revenueByCategory = OrderItem::whereHas('order', fn ($q) => $q->where('created_at', '>=', $start))
            ->selectRaw('menu_item_id, SUM(subtotal) as revenue')
            ->groupBy('menu_item_id')
            ->with('menuItem.category:id,name')
            ->get()
            ->groupBy(fn ($item) => $item->menuItem?->category?->name ?? 'Unknown')
            ->map->sum('revenue')
            ->sortDesc()
            ->take(8);

        $hourlyOrders = collect(range(0, 23))->map(fn ($h) => [
            'hour' => sprintf('%02d:00', $h),
            'count' => Order::where('created_at', '>=', $start)->whereRaw('HOUR(created_at) = ?', [$h])->count(),
        ]);

        return view('livewire.admin.analytics', compact('stats', 'revenueByDay', 'ordersByType', 'ordersByStatus', 'topItems', 'revenueByCategory', 'hourlyOrders'));
    }
}
