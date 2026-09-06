<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'DejaVu Sans', sans-serif; font-size: 13px; color: #1c1917; background: #fff; }
.page { padding: 40px 48px; }

/* Header */
.header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 36px; }
.brand-name { font-size: 26px; font-weight: 700; color: #d97706; letter-spacing: -0.5px; }
.brand-sub { font-size: 11px; color: #78716c; margin-top: 2px; }
.invoice-meta { text-align: right; }
.invoice-meta .label { font-size: 10px; color: #78716c; text-transform: uppercase; letter-spacing: 0.5px; }
.invoice-meta .value { font-size: 13px; font-weight: 600; color: #1c1917; margin-top: 1px; }
.invoice-meta .number { font-size: 18px; font-weight: 700; color: #d97706; font-family: monospace; }

/* Divider */
.divider { border: none; border-top: 1px solid #e7e5e4; margin: 20px 0; }
.divider-bold { border: none; border-top: 2px solid #d97706; margin: 20px 0; }

/* Info grid */
.info-grid { display: flex; gap: 0; margin-bottom: 28px; }
.info-col { flex: 1; }
.info-col h4 { font-size: 10px; color: #78716c; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
.info-col p { font-size: 13px; color: #1c1917; margin-bottom: 2px; }
.info-col .muted { color: #78716c; font-size: 12px; }

/* Status badge */
.status { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.status-pending { background: #fef9c3; color: #a16207; }
.status-confirmed { background: #dbeafe; color: #1d4ed8; }
.status-preparing { background: #ffedd5; color: #c2410c; }
.status-ready { background: #dcfce7; color: #15803d; }
.status-completed,.status-served { background: #f1f5f9; color: #475569; }
.status-cancelled { background: #fee2e2; color: #b91c1c; }

/* Items table */
table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
thead tr { background: #292524; }
thead th { padding: 10px 12px; text-align: left; font-size: 11px; color: #fafaf9; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; }
thead th:last-child { text-align: right; }
tbody tr { border-bottom: 1px solid #f5f5f4; }
tbody tr:last-child { border-bottom: none; }
tbody td { padding: 10px 12px; font-size: 13px; }
tbody td:last-child { text-align: right; font-weight: 600; }
tbody tr:nth-child(even) { background: #fafaf9; }
.item-name { font-weight: 600; color: #1c1917; }
.item-sub { font-size: 11px; color: #78716c; margin-top: 1px; }

/* Totals */
.totals { margin-left: auto; width: 260px; }
.totals-row { display: flex; justify-content: space-between; padding: 5px 0; font-size: 13px; color: #44403c; }
.totals-row.discount { color: #16a34a; }
.totals-row.total { font-size: 16px; font-weight: 700; color: #1c1917; border-top: 2px solid #e7e5e4; padding-top: 10px; margin-top: 6px; }
.totals-row.total span:last-child { color: #d97706; }

/* Points info */
.points-box { background: #fffbeb; border: 1px solid #fde68a; border-radius: 6px; padding: 10px 14px; margin-bottom: 20px; font-size: 12px; color: #92400e; }
.points-box strong { color: #b45309; }

/* Footer */
.footer { margin-top: 40px; text-align: center; font-size: 11px; color: #a8a29e; border-top: 1px solid #e7e5e4; padding-top: 20px; }
.footer strong { color: #78716c; }

/* Payment badge */
.payment-badge { display: inline-block; padding: 2px 8px; background: #f0fdf4; color: #15803d; border-radius: 4px; font-size: 11px; font-weight: 600; border: 1px solid #bbf7d0; }

.type-badge { display: inline-block; padding: 2px 8px; background: #f8fafc; color: #475569; border-radius: 4px; font-size: 11px; border: 1px solid #e2e8f0; }
</style>
</head>
<body>
<div class="page">

    {{-- Header --}}
    <div class="header">
        <div>
            <div class="brand-name">Matebeto</div>
            <div class="brand-sub">RESTAURANT &amp; BAR • Lusaka, Zambia</div>
            <div class="brand-sub" style="margin-top:4px">Tel: +260 97X XXX XXXX</div>
        </div>
        <div class="invoice-meta">
            <div class="label">Invoice / Receipt</div>
            <div class="number">{{ $order->order_number }}</div>
            <div class="value" style="margin-top:4px">{{ $order->created_at->format('d M Y, h:i A') }}</div>
            <div style="margin-top:6px">
                <span class="status status-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
            </div>
        </div>
    </div>

    <hr class="divider-bold">

    {{-- Customer + Order Info --}}
    <div class="info-grid">
        <div class="info-col">
            <h4>Billed To</h4>
            <p><strong>{{ $order->customer_name }}</strong></p>
            <p class="muted">{{ $order->customer_phone }}</p>
            @if($order->customer_email)<p class="muted">{{ $order->customer_email }}</p>@endif
            @if($order->delivery_address)<p class="muted">{{ $order->delivery_address }}</p>@endif
        </div>
        <div class="info-col" style="text-align:center">
            <h4>Order Type</h4>
            <p>
                <span class="type-badge">
                    @if($order->order_type === 'dine_in') Dine In
                    @elseif($order->order_type === 'takeaway') Takeaway
                    @else Delivery
                    @endif
                </span>
            </p>
            @if($order->table)<p class="muted" style="margin-top:4px">Table {{ $order->table->number }}</p>@endif
        </div>
        <div class="info-col" style="text-align:right">
            <h4>Payment</h4>
            <p>
                <span class="payment-badge">
                    @php $pmLabels = [
                        'cash'          => 'Cash',
                        'card'          => 'Visa / Mastercard',
                        'airtel_money'  => 'Airtel Money',
                        'mtn_momo'      => 'MTN Mobile Money',
                        'zamtel_kwacha' => 'Zamtel Kwacha',
                        'zampay'        => 'ZamPay',
                        'bank_transfer' => 'Bank Transfer',
                    ]; @endphp
                    {{ $pmLabels[$order->payment_method] ?? ucfirst(str_replace('_', ' ', $order->payment_method)) }}
                </span>
            </p>
            <p class="muted" style="margin-top:4px">{{ ucfirst($order->payment_status) }}</p>
            @if($latestPayment && $latestPayment->status === 'succeeded')
            <p class="muted" style="margin-top:4px">Ref: {{ substr($latestPayment->reference, -8) }}</p>
            <p class="muted" style="margin-top:2px">{{ $latestPayment->confirmed_at?->format('d M Y, h:i A') }}</p>
            @endif
        </div>
    </div>

    <hr class="divider">

    {{-- Items Table --}}
    <table>
        <thead>
            <tr>
                <th style="width:50%">Item</th>
                <th style="width:15%;text-align:center">Qty</th>
                <th style="width:17%;text-align:right">Unit Price</th>
                <th style="width:18%;text-align:right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>
                    <div class="item-name">{{ $item->menuItem?->name ?? 'Item' }}</div>
                    @if($item->special_instructions)
                    <div class="item-sub">Note: {{ $item->special_instructions }}</div>
                    @endif
                </td>
                <td style="text-align:center">{{ $item->quantity }}</td>
                <td style="text-align:right">K {{ number_format($item->unit_price, 2) }}</td>
                <td>K {{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Loyalty points box --}}
    @if($order->loyalty_points_redeemed > 0 || $order->loyalty_points_earned > 0)
    <div class="points-box">
        @if($order->loyalty_points_redeemed > 0)
        <strong>⭐ {{ number_format($order->loyalty_points_redeemed) }} loyalty points redeemed</strong>
        (saved K {{ number_format($order->loyalty_points_redeemed / 10, 2) }})
        @endif
        @if($order->loyalty_points_earned > 0)
        &nbsp;&nbsp;|&nbsp;&nbsp; <strong>+{{ number_format($order->loyalty_points_earned) }} points earned</strong> on this order
        @endif
    </div>
    @endif

    {{-- Totals --}}
    <div class="totals">
        <div class="totals-row"><span>Subtotal</span><span>K {{ number_format($order->subtotal, 2) }}</span></div>
        @if($order->discount > 0)
        <div class="totals-row discount"><span>Discount</span><span>−K {{ number_format($order->discount, 2) }}</span></div>
        @endif
        <div class="totals-row"><span>Tax (16% VAT)</span><span>K {{ number_format($order->tax, 2) }}</span></div>
        @if($order->delivery_fee > 0)
        <div class="totals-row"><span>Delivery Fee</span><span>K {{ number_format($order->delivery_fee, 2) }}</span></div>
        @endif
        <div class="totals-row total"><span>Total</span><span>K {{ number_format($order->total, 2) }}</span></div>
    </div>

    @if($order->notes)
    <div style="margin-top:20px;padding:10px 14px;background:#f8fafc;border-radius:6px;font-size:12px;color:#475569;border-left:3px solid #d97706">
        <strong>Special notes:</strong> {{ $order->notes }}
    </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <p><strong>Thank you for dining with us!</strong></p>
        <p style="margin-top:4px">Matebeto Restaurant &amp; Bar • Lusaka, Zambia</p>
        <p style="margin-top:4px">This is a computer-generated receipt and does not require a signature.</p>
        <p style="margin-top:4px">Printed on {{ now()->format('d M Y H:i') }}</p>
    </div>

</div>
</body>
</html>
