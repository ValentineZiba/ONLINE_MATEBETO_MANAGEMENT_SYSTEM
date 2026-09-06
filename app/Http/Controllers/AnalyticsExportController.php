<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalyticsExportController extends Controller
{
    public function orders(Request $request): StreamedResponse
    {
        $days  = max(1, min(365, (int) $request->get('period', 30)));
        $start = now()->subDays($days)->startOfDay();

        $orders = Order::with(['items.menuItem', 'table'])
            ->where('created_at', '>=', $start)
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'orders_' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($orders) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Order #', 'Date', 'Status', 'Type', 'Table',
                'Customer', 'Phone', 'Items', 'Subtotal (K)',
                'Tax (K)', 'Delivery Fee (K)', 'Discount (K)', 'Total (K)',
                'Payment Method', 'Payment Status', 'Driver',
            ]);

            foreach ($orders as $order) {
                fputcsv($handle, [
                    $order->order_number,
                    $order->created_at->format('Y-m-d H:i'),
                    $order->status,
                    str_replace('_', ' ', $order->order_type),
                    $order->table?->number ?? '—',
                    $order->customer_name,
                    $order->customer_phone,
                    $order->items->sum('quantity'),
                    number_format($order->subtotal, 2),
                    number_format($order->tax, 2),
                    number_format($order->delivery_fee ?? 0, 2),
                    number_format($order->discount ?? 0, 2),
                    number_format($order->total, 2),
                    str_replace('_', ' ', $order->payment_method),
                    $order->payment_status,
                    $order->driver_name ?? '—',
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function topItems(Request $request): StreamedResponse
    {
        $days  = max(1, min(365, (int) $request->get('period', 30)));
        $start = now()->subDays($days)->startOfDay();

        $items = OrderItem::whereHas('order', fn ($q) => $q->where('created_at', '>=', $start))
            ->selectRaw('menu_item_id, SUM(quantity) as total_qty, SUM(subtotal) as total_revenue')
            ->groupBy('menu_item_id')
            ->orderByDesc('total_qty')
            ->with('menuItem:id,name,price')
            ->get();

        $filename = 'top_items_' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($items) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Item Name', 'Qty Sold', 'Revenue (K)']);
            foreach ($items as $item) {
                fputcsv($handle, [
                    $item->menuItem?->name ?? 'Unknown',
                    $item->total_qty,
                    number_format($item->total_revenue, 2),
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
