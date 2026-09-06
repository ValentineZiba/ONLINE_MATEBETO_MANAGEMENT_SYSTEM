<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  body { font-family: Arial, sans-serif; background:#f5f5f5; margin:0; padding:0; }
  .wrap { max-width:580px; margin:32px auto; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.08); }
  .header { background:#92400e; padding:32px; text-align:center; }
  .header h1 { color:#fff; margin:0; font-size:24px; }
  .header p { color:#fcd34d; margin:6px 0 0; font-size:14px; }
  .body { padding:32px; }
  .badge { display:inline-block; background:#fef3c7; color:#92400e; font-size:22px; font-weight:bold; padding:8px 24px; border-radius:8px; letter-spacing:1px; margin-bottom:20px; }
  table { width:100%; border-collapse:collapse; margin:16px 0; }
  th { background:#f9fafb; text-align:left; padding:10px 12px; font-size:12px; color:#6b7280; text-transform:uppercase; }
  td { padding:10px 12px; border-bottom:1px solid #f3f4f6; font-size:14px; color:#374151; }
  .total-row td { font-weight:bold; font-size:16px; color:#92400e; border-top:2px solid #e5e7eb; border-bottom:none; }
  .footer { background:#f9fafb; padding:20px 32px; text-align:center; font-size:12px; color:#9ca3af; }
  .btn { display:inline-block; background:#d97706; color:#fff; padding:12px 28px; border-radius:8px; text-decoration:none; font-weight:bold; margin-top:16px; }
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <h1>🍽️ {{ config('app.name') }}</h1>
    <p>Your order has been confirmed!</p>
  </div>
  <div class="body">
    <p style="color:#374151;">Hi <strong>{{ $order->customer_name }}</strong>,</p>
    <p style="color:#6b7280;font-size:14px;">Thank you for your order. We've received it and are getting started!</p>

    <div style="text-align:center;">
      <div class="badge">#{{ $order->order_number }}</div>
    </div>

    <table>
      <tr><th>Item</th><th>Qty</th><th style="text-align:right;">Price</th></tr>
      @foreach($order->items as $item)
      <tr>
        <td>{{ $item->menuItem?->name ?? 'Item' }}</td>
        <td>{{ $item->quantity }}</td>
        <td style="text-align:right;">K {{ number_format($item->subtotal, 2) }}</td>
      </tr>
      @endforeach
      <tr><td colspan="2" style="text-align:right;color:#6b7280;">Subtotal</td><td style="text-align:right;">K {{ number_format($order->subtotal, 2) }}</td></tr>
      <tr><td colspan="2" style="text-align:right;color:#6b7280;">Tax (16%)</td><td style="text-align:right;">K {{ number_format($order->tax, 2) }}</td></tr>
      @if($order->delivery_fee > 0)
      <tr><td colspan="2" style="text-align:right;color:#6b7280;">Delivery</td><td style="text-align:right;">K {{ number_format($order->delivery_fee, 2) }}</td></tr>
      @endif
      <tr class="total-row"><td colspan="2">Total</td><td style="text-align:right;">K {{ number_format($order->total, 2) }}</td></tr>
    </table>

    <p style="font-size:13px;color:#6b7280;">
      <strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $order->order_type)) }} &nbsp;|&nbsp;
      <strong>Payment:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }} &nbsp;|&nbsp;
      <strong>Est. time:</strong> {{ $order->estimated_minutes }} mins
    </p>

    <div style="text-align:center;">
      <a href="{{ url('/track-order?order=' . $order->order_number) }}" class="btn">Track My Order →</a>
    </div>
  </div>
  <div class="footer">{{ config('app.name') }} · Thank you for dining with us!</div>
</div>
</body>
</html>
