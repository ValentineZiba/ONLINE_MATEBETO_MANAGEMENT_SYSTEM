<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function download(Order $order)
    {
        // Customer can only view their own orders; admin/manager can view any
        if (auth()->id() !== $order->user_id && !in_array(auth()->user()->role, ['admin', 'manager'])) {
            abort(403);
        }

        $order->load('items.menuItem', 'paymentTransactions');

        $pdf = Pdf::loadView('pdf.invoice', [
            'order' => $order,
            'latestPayment' => $order->paymentTransactions->sortByDesc('id')->first(),
        ])->setPaper('a4', 'portrait');

        return $pdf->download("Matebeto-Invoice-{$order->order_number}.pdf");
    }
}
