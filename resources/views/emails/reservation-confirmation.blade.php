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
  .detail-row { display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid #f3f4f6; font-size:14px; }
  .detail-label { color:#9ca3af; }
  .detail-value { color:#374151; font-weight:600; }
  .footer { background:#f9fafb; padding:20px 32px; text-align:center; font-size:12px; color:#9ca3af; }
</style>
</head>
<body>
<div class="wrap">
  <div class="header">
    <h1>🍽️ Reservation Confirmed!</h1>
    <p>We look forward to seeing you</p>
  </div>
  <div class="body">
    <p style="color:#374151;">Hi <strong>{{ $reservation->name }}</strong>,</p>
    <p style="color:#6b7280;font-size:14px;">Your reservation at <strong>{{ config('app.name') }}</strong> has been confirmed. Here are your details:</p>

    <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:20px;margin:20px 0;">
      <div class="detail-row"><span class="detail-label">📅 Date</span><span class="detail-value">{{ \Carbon\Carbon::parse($reservation->date)->format('D, M j, Y') }}</span></div>
      <div class="detail-row"><span class="detail-label">🕐 Time</span><span class="detail-value">{{ \Carbon\Carbon::parse($reservation->time)->format('g:i A') }}</span></div>
      <div class="detail-row"><span class="detail-label">👥 Party Size</span><span class="detail-value">{{ $reservation->party_size }} {{ Str::plural('guest', $reservation->party_size) }}</span></div>
      @if($reservation->occasion)<div class="detail-row"><span class="detail-label">🎉 Occasion</span><span class="detail-value">{{ ucfirst($reservation->occasion) }}</span></div>@endif
      @if($reservation->special_requests)<div class="detail-row"><span class="detail-label">📝 Special Requests</span><span class="detail-value">{{ $reservation->special_requests }}</span></div>@endif
    </div>

    <p style="font-size:13px;color:#6b7280;">Need to cancel or make changes? Contact us as soon as possible so we can accommodate other guests.</p>
  </div>
  <div class="footer">{{ config('app.name') }} · We can't wait to host you!</div>
</div>
</body>
</html>
