<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  body { font-family: Arial, sans-serif; background:#f5f5f5; margin:0; padding:0; }
  .wrap { max-width:580px; margin:32px auto; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.08); }
  .header { background:#92400e; padding:32px; text-align:center; }
  .header h1 { color:#fff; margin:0; font-size:24px; }
  .body { padding:32px; }
  .status-badge { display:inline-block; padding:8px 20px; border-radius:999px; font-weight:bold; font-size:15px; margin:12px 0; }
  .footer { background:#f9fafb; padding:20px 32px; text-align:center; font-size:12px; color:#9ca3af; }
  .btn { display:inline-block; background:#d97706; color:#fff; padding:12px 28px; border-radius:8px; text-decoration:none; font-weight:bold; margin-top:16px; }
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <h1>🍽️ Order Update</h1>
  </div>
  <div class="body">
    <p style="color:#374151;">Hi <strong>{{ $order->customer_name }}</strong>,</p>
    <p style="color:#6b7280;">Your order <strong>#{{ $order->order_number }}</strong> status has been updated.</p>

    <div style="text-align:center;margin:20px 0;">
      @php
        $colors = ['pending'=>'#fef3c7;color:#92400e','confirmed'=>'#dbeafe;color:#1d4ed8','preparing'=>'#ffedd5;color:#c2410c','ready'=>'#d1fae5;color:#065f46','completed'=>'#f3f4f6;color:#374151','cancelled'=>'#fee2e2;color:#dc2626'];
        $bg = $colors[$order->status] ?? '#f3f4f6;color:#374151';
      @endphp
      <span class="status-badge" style="background:{{ explode(';', $bg)[0] }};{{ explode(';', $bg)[1] ?? '' }}">
        {{ $order->status_label }}
      </span>
    </div>

    @if($order->status === 'ready')
    <p style="text-align:center;color:#065f46;font-weight:bold;">🎉 Your order is ready for pickup / will be served shortly!</p>
    @elseif($order->status === 'completed')
    <p style="text-align:center;color:#374151;">Thank you for dining with us! We hope to see you again soon.</p>
    @endif

    <div style="text-align:center;">
      <a href="{{ url('/track-order?order=' . $order->order_number) }}" class="btn">Track Order →</a>
    </div>
  </div>
  <div class="footer">{{ config('app.name') }} · Thank you for your order!</div>
</div>
</body>
</html>
