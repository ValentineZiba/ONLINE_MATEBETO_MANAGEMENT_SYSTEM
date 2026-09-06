<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\Reservation;
use App\Models\RestaurantTable;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Polling;
use Livewire\Component;

#[Layout('components.layouts.admin', ['title' => 'Dashboard'])]
class Dashboard extends Component
{
    #[Polling('30s')]
    public function render()
    {
        $todayOrders = Order::today();
        $monthOrders = Order::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);

        $stats = [
            'today_orders' => $todayOrders->count(),
            'today_revenue' => $todayOrders->where('payment_status', 'paid')->sum('total'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'preparing_orders' => Order::whereIn('status', ['confirmed', 'preparing'])->count(),
            'available_tables' => RestaurantTable::where('status', 'available')->count(),
            'occupied_tables' => RestaurantTable::where('status', 'occupied')->count(),
            'today_reservations' => Reservation::today()->count(),
            'month_revenue' => $monthOrders->where('payment_status', 'paid')->sum('total'),
            'total_customers' => User::where('role', 'customer')->count(),
            'monthly_orders' => $monthOrders->count(),
        ];

        $recentOrders = Order::with(['user', 'items'])
            ->latest()
            ->take(8)
            ->get();

        $todayReservations = Reservation::today()
            ->with('table')
            ->orderBy('reservation_time')
            ->take(8)
            ->get();

        $revenueByDay = collect(range(6, 0))->map(fn ($d) => [
            'label' => now()->subDays($d)->format('D'),
            'revenue' => Order::whereDate('created_at', now()->subDays($d))
                ->where('payment_status', 'paid')
                ->sum('total'),
        ]);

        return view('livewire.admin.dashboard', compact('stats', 'recentOrders', 'todayReservations', 'revenueByDay'));
    }
}
