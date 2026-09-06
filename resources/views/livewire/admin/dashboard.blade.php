<div class="space-y-6">
    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        @php
        $cards = [
            ['label' => "Today's Revenue", 'value' => 'K ' . number_format($stats['today_revenue'], 0), 'icon' => '💰', 'color' => 'amber', 'border' => 'border-l-amber-500', 'sub' => $stats['today_orders'] . ' orders today'],
            ['label' => 'Pending Orders', 'value' => $stats['pending_orders'], 'icon' => '⏳', 'color' => 'yellow', 'border' => 'border-l-yellow-400', 'sub' => 'Awaiting confirmation'],
            ['label' => 'Preparing', 'value' => $stats['preparing_orders'], 'icon' => '👨‍🍳', 'color' => 'orange', 'border' => 'border-l-orange-500', 'sub' => 'In the kitchen'],
            ['label' => 'Available Tables', 'value' => $stats['available_tables'] . '/' . ($stats['available_tables'] + $stats['occupied_tables']), 'icon' => '🪑', 'color' => 'green', 'border' => 'border-l-green-500', 'sub' => $stats['occupied_tables'] . ' occupied'],
            ['label' => "Today's Reservations", 'value' => $stats['today_reservations'], 'icon' => '📅', 'color' => 'blue', 'border' => 'border-l-blue-500', 'sub' => 'For today'],
        ];
        @endphp
        @foreach($cards as $card)
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-stone-100 border-l-4 {{ $card['border'] }} hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <span class="text-2xl">{{ $card['icon'] }}</span>
                <span class="text-xs px-2 py-1 rounded-full bg-{{ $card['color'] }}-50 text-{{ $card['color'] }}-700 font-medium">Live</span>
            </div>
            <div class="text-2xl font-bold text-stone-800 mb-1">{{ $card['value'] }}</div>
            <div class="text-sm font-medium text-stone-600">{{ $card['label'] }}</div>
            <div class="text-xs text-stone-400 mt-0.5">{{ $card['sub'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Revenue Chart --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-stone-100">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="font-semibold text-stone-800">Revenue — Last 7 Days</h3>
                <div class="text-sm text-stone-400">K {{ number_format($stats['month_revenue'], 0) }} this month</div>
            </div>
            <a href="{{ route('admin.analytics') }}" class="text-sm text-amber-600 hover:text-amber-700 font-medium">Full Analytics →</a>
        </div>
        @php $maxRevenue = $revenueByDay->max('revenue') ?: 1; @endphp
        <div class="flex items-end gap-2 h-40">
            @foreach($revenueByDay as $day)
            @php $height = max(4, round(($day['revenue'] / $maxRevenue) * 100)); @endphp
            <div class="flex-1 flex flex-col items-center gap-1">
                <div class="text-xs text-stone-500 font-medium">{{ $day['revenue'] > 0 ? 'K ' . number_format($day['revenue']/1000, 1) . 'k' : '—' }}</div>
                <div class="w-full rounded-t-lg transition-all bg-gradient-to-t from-amber-500 to-amber-400 hover:from-amber-600 hover:to-amber-500"
                     style="height: {{ $height }}%"></div>
                <div class="text-xs text-stone-400">{{ $day['label'] }}</div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent Orders --}}
        <div class="bg-white rounded-2xl shadow-sm border border-stone-100 overflow-hidden">
            <div class="flex items-center justify-between p-6 border-b border-stone-50">
                <h3 class="font-semibold text-stone-800">Recent Orders</h3>
                <a href="{{ route('admin.orders') }}" class="text-sm text-amber-600 hover:text-amber-700 font-medium">View All →</a>
            </div>
            <div class="divide-y divide-stone-50">
                @forelse($recentOrders as $order)
                <div class="flex items-center gap-4 px-6 py-3 hover:bg-stone-50 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center text-lg flex-shrink-0">
                        {{ match($order->order_type) { 'dine_in' => '🍽️', 'takeaway' => '📦', 'delivery' => '🚴', default => '📋' } }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-stone-800 text-sm font-mono">{{ $order->order_number }}</div>
                        <div class="text-stone-400 text-xs truncate">{{ $order->customer_name }} • {{ $order->items->count() }} items</div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <div class="font-semibold text-stone-700 text-sm">K {{ number_format($order->total, 0) }}</div>
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium
                            {{ match($order->status) {
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'confirmed','preparing' => 'bg-orange-100 text-orange-700',
                                'ready' => 'bg-green-100 text-green-700',
                                'completed' => 'bg-stone-100 text-stone-600',
                                'cancelled' => 'bg-red-100 text-red-600',
                                default => 'bg-stone-100 text-stone-600',
                            } }}">{{ ucfirst($order->status) }}</span>
                    </div>
                </div>
                @empty
                <div class="text-center text-stone-400 py-8">No orders yet</div>
                @endforelse
            </div>
        </div>

        {{-- Today's Reservations --}}
        <div class="bg-white rounded-2xl shadow-sm border border-stone-100 overflow-hidden">
            <div class="flex items-center justify-between p-6 border-b border-stone-50">
                <h3 class="font-semibold text-stone-800">Today's Reservations</h3>
                <a href="{{ route('admin.reservations') }}" class="text-sm text-amber-600 hover:text-amber-700 font-medium">View All →</a>
            </div>
            <div class="divide-y divide-stone-50">
                @forelse($todayReservations as $res)
                <div class="flex items-center gap-4 px-6 py-3 hover:bg-stone-50 transition-colors">
                    <div class="text-center w-14 flex-shrink-0">
                        <div class="text-lg font-bold text-stone-800">{{ \Carbon\Carbon::parse($res->reservation_time)->format('h:i') }}</div>
                        <div class="text-xs text-stone-400">{{ \Carbon\Carbon::parse($res->reservation_time)->format('A') }}</div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-stone-800 text-sm truncate">{{ $res->guest_name }}</div>
                        <div class="text-stone-400 text-xs">{{ $res->party_size }} guests{{ $res->table ? ' • ' . $res->table->number : '' }}</div>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full font-medium flex-shrink-0
                        {{ match($res->status) {
                            'pending' => 'bg-yellow-100 text-yellow-700',
                            'confirmed' => 'bg-blue-100 text-blue-700',
                            'seated' => 'bg-green-100 text-green-700',
                            'completed' => 'bg-stone-100 text-stone-600',
                            default => 'bg-stone-100 text-stone-600',
                        } }}">{{ ucfirst($res->status) }}</span>
                </div>
                @empty
                <div class="text-center text-stone-400 py-8">No reservations today</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @foreach([
            ['href' => route('pos.terminal'), 'icon' => '🖥️', 'label' => 'POS Terminal', 'desc' => 'Take orders at counter'],
            ['href' => route('kitchen.display'), 'icon' => '👨‍🍳', 'label' => 'Kitchen Display', 'desc' => 'View active orders'],
            ['href' => route('admin.menu.items'), 'icon' => '📋', 'label' => 'Manage Menu', 'desc' => 'Update items & prices'],
            ['href' => route('admin.analytics'), 'icon' => '📊', 'label' => 'Analytics', 'desc' => 'Revenue & insights'],
        ] as $action)
        <a href="{{ $action['href'] }}" class="bg-white rounded-2xl p-5 shadow-sm border border-stone-100 hover:border-amber-200 hover:shadow-md transition-all group">
            <div class="text-3xl mb-3">{{ $action['icon'] }}</div>
            <div class="font-semibold text-stone-800 group-hover:text-amber-700 transition-colors text-sm">{{ $action['label'] }}</div>
            <div class="text-xs text-stone-400 mt-1">{{ $action['desc'] }}</div>
        </a>
        @endforeach
    </div>
</div>
