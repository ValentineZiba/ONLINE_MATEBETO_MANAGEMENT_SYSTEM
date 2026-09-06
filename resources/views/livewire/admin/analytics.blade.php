<div class="space-y-6">
    {{-- Period Filter + Export --}}
    <div class="flex flex-wrap items-center gap-3 justify-between">
        <div class="flex items-center gap-3 flex-wrap">
            <span class="text-sm font-medium text-stone-600">Period:</span>
            @foreach(['7' => 'Last 7 days', '30' => 'Last 30 days', '90' => 'Last 90 days', '365' => 'Last year'] as $val => $label)
            <button wire:click="$set('period', '{{ $val }}')"
                    class="px-4 py-2 text-sm rounded-xl font-medium transition-colors {{ $period == $val ? 'bg-amber-600 text-white' : 'bg-white text-stone-600 border border-stone-200 hover:border-amber-300' }}">
                {{ $label }}
            </button>
            @endforeach
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.analytics.export.orders', ['period' => $period]) }}"
               class="flex items-center gap-1.5 px-4 py-2 text-sm font-medium rounded-xl border border-stone-200 text-stone-600 hover:border-amber-400 hover:text-amber-700 bg-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Orders CSV
            </a>
            <a href="{{ route('admin.analytics.export.items', ['period' => $period]) }}"
               class="flex items-center gap-1.5 px-4 py-2 text-sm font-medium rounded-xl border border-stone-200 text-stone-600 hover:border-amber-400 hover:text-amber-700 bg-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Items CSV
            </a>
        </div>
    </div>

    {{-- KPI Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
        @php $kpis = [
            ['label' => 'Total Revenue', 'value' => 'K ' . number_format($stats['total_revenue'], 0), 'icon' => '💰', 'color' => 'amber'],
            ['label' => 'Total Orders', 'value' => number_format($stats['total_orders']), 'icon' => '📦', 'color' => 'blue'],
            ['label' => 'Avg Order Value', 'value' => 'K ' . number_format($stats['avg_order_value'], 0), 'icon' => '📊', 'color' => 'purple'],
            ['label' => 'New Customers', 'value' => $stats['new_customers'], 'icon' => '👥', 'color' => 'green'],
            ['label' => 'Reservations', 'value' => $stats['total_reservations'], 'icon' => '📅', 'color' => 'pink'],
            ['label' => 'Completion Rate', 'value' => $stats['completion_rate'] . '%', 'icon' => '✅', 'color' => 'teal'],
        ]; @endphp
        @foreach($kpis as $kpi)
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-stone-100">
            <div class="text-3xl mb-3">{{ $kpi['icon'] }}</div>
            <div class="text-2xl font-bold text-stone-800">{{ $kpi['value'] }}</div>
            <div class="text-sm text-stone-400 mt-1">{{ $kpi['label'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Revenue Chart --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-stone-100">
        <h3 class="font-semibold text-stone-800 mb-6">Daily Revenue</h3>
        @php $maxR = $revenueByDay->max('revenue') ?: 1; @endphp
        <div class="flex items-end gap-1 h-48 mb-2">
            @foreach($revenueByDay as $day)
            @php $h = max(2, round(($day['revenue'] / $maxR) * 100)); @endphp
            <div class="flex-1 flex flex-col items-center gap-1 group" title="{{ $day['date'] }}: K {{ number_format($day['revenue'], 0) }}">
                <div class="w-full rounded-t-lg bg-gradient-to-t from-amber-500 to-amber-300 hover:from-amber-600 hover:to-amber-400 transition-colors cursor-pointer"
                     style="height: {{ $h }}%"></div>
            </div>
            @endforeach
        </div>
        <div class="flex gap-1">
            @foreach($revenueByDay as $day)
            <div class="flex-1 text-center text-xs text-stone-400">{{ $day['date'] }}</div>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Orders by Type --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-stone-100">
            <h3 class="font-semibold text-stone-800 mb-5">Orders by Type</h3>
            @php $total = array_sum($ordersByType) ?: 1; @endphp
            <div class="space-y-4">
                @foreach(['dine_in' => ['🍽️ Dine In', 'amber'], 'takeaway' => ['📦 Takeaway', 'blue'], 'delivery' => ['🚴 Delivery', 'purple']] as $key => [$label, $color])
                @php $pct = round(($ordersByType[$key] / $total) * 100); @endphp
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-stone-700">{{ $label }}</span>
                        <span class="text-stone-500 font-medium">{{ $ordersByType[$key] }} ({{ $pct }}%)</span>
                    </div>
                    <div class="h-2 bg-stone-100 rounded-full overflow-hidden">
                        <div class="h-full bg-{{ $color }}-500 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Top Menu Items --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-stone-100">
            <h3 class="font-semibold text-stone-800 mb-5">Top Selling Items</h3>
            <div class="space-y-3">
                @forelse($topItems->take(8) as $i => $item)
                <div class="flex items-center gap-3">
                    <span class="text-sm font-bold text-stone-400 w-5">{{ $i + 1 }}</span>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-stone-700 truncate">{{ $item->menuItem?->name ?? '—' }}</div>
                        <div class="text-xs text-stone-400">{{ $item->total_qty }} sold</div>
                    </div>
                    <div class="text-sm font-semibold text-amber-600">K {{ number_format($item->total_revenue, 0) }}</div>
                </div>
                @empty
                <div class="text-stone-400 text-sm">No data yet</div>
                @endforelse
            </div>
        </div>

        {{-- Revenue by Category --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-stone-100">
            <h3 class="font-semibold text-stone-800 mb-5">Revenue by Category</h3>
            @php $catMax = $revenueByCategory->max() ?: 1; @endphp
            <div class="space-y-3">
                @forelse($revenueByCategory as $catName => $revenue)
                @php $pct = round(($revenue / $catMax) * 100); @endphp
                <div>
                    <div class="flex justify-between text-sm mb-1"><span class="text-stone-700 truncate">{{ $catName }}</span><span class="text-stone-500 font-medium ml-2 flex-shrink-0">K {{ number_format($revenue, 0) }}</span></div>
                    <div class="h-1.5 bg-stone-100 rounded-full overflow-hidden"><div class="h-full bg-amber-500 rounded-full" style="width: {{ $pct }}%"></div></div>
                </div>
                @empty
                <div class="text-stone-400 text-sm">No data</div>
                @endforelse
            </div>
        </div>

        {{-- Hourly Orders Heatmap --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-stone-100">
            <h3 class="font-semibold text-stone-800 mb-5">Peak Hours</h3>
            @php $hourMax = $hourlyOrders->max('count') ?: 1; @endphp
            <div class="grid grid-cols-12 gap-1">
                @foreach($hourlyOrders as $hour)
                @php $intensity = round(($hour['count'] / $hourMax) * 100); @endphp
                <div class="text-center" title="{{ $hour['hour'] }}: {{ $hour['count'] }} orders">
                    <div class="h-10 rounded flex items-end overflow-hidden bg-stone-50">
                        <div class="w-full rounded bg-amber-{{ $intensity > 75 ? '600' : ($intensity > 50 ? '400' : ($intensity > 25 ? '300' : '100')) }} transition-all"
                             style="height: {{ max(10, $intensity) }}%"></div>
                    </div>
                    <div class="text-xs text-stone-400 mt-1">{{ substr($hour['hour'], 0, 2) }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
