<div x-data class="min-h-screen bg-stone-950 text-white p-4">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div class="text-2xl">🍳</div>
            <div>
                <h1 class="text-xl font-bold">Kitchen Display</h1>
                <p class="text-stone-400 text-xs">Auto-refreshes every 15s</p>
            </div>
        </div>
        <div class="flex items-center gap-4 text-sm">
            <div class="flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-red-500 animate-pulse"></span>
                <span class="text-stone-300">{{ $stats['pending'] }} new</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-orange-400 animate-pulse"></span>
                <span class="text-stone-300">{{ $stats['preparing'] }} cooking</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-green-400"></span>
                <span class="text-stone-300">{{ $stats['ready'] }} ready</span>
            </div>
        </div>
    </div>

    @php $allOrders = collect($grouped)->flatten(1); @endphp

    @if($allOrders->isEmpty())
    <div class="flex items-center justify-center h-[60vh]">
        <div class="text-center">
            <div class="text-6xl mb-4">✅</div>
            <h2 class="text-2xl font-bold text-stone-300">All clear!</h2>
            <p class="text-stone-500 mt-2">No active orders in the queue</p>
        </div>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach($allOrders as $order)
        @php
            $age = $order->created_at->diffInMinutes(now());
            $borderColor = $order->status === 'ready' ? 'border-green-500' : ($age > 20 ? 'border-red-500' : ($age > 10 ? 'border-orange-400' : 'border-stone-700'));
            $headerBg = $order->status === 'ready' ? 'bg-green-900/40' : ($order->status === 'preparing' ? 'bg-orange-900/40' : 'bg-stone-800');
        @endphp
        <div class="rounded-2xl border-2 {{ $borderColor }} bg-stone-900 overflow-hidden flex flex-col">
            {{-- Order Header --}}
            <div class="{{ $headerBg }} px-4 py-3 flex items-center justify-between">
                <div>
                    <div class="font-bold text-lg">#{{ $order->order_number }}</div>
                    <div class="text-xs text-stone-400">
                        {{ $age }}min ago •
                        {{ $order->order_type === 'dine_in' ? '🍽️ Table ' . ($order->table?->number ?? '?') : ($order->order_type === 'takeaway' ? '📦 Takeaway' : '🚴 Delivery') }}
                    </div>
                </div>
                <div class="text-right">
                    @php $statusColors = ['pending' => 'bg-red-500', 'confirmed' => 'bg-blue-500', 'preparing' => 'bg-orange-500', 'ready' => 'bg-green-500']; @endphp
                    <span class="text-xs px-2.5 py-1 rounded-full font-bold {{ $statusColors[$order->status] ?? 'bg-stone-600' }}">
                        {{ strtoupper($order->status) }}
                    </span>
                    @if($age > 15 && $order->status !== 'ready')
                    <div class="text-red-400 text-xs font-bold mt-1">⚠️ LATE</div>
                    @endif
                </div>
            </div>

            {{-- Order Items --}}
            <div class="p-4 flex-1 space-y-2">
                @foreach($order->items as $item)
                <div class="flex items-start gap-3 p-2.5 rounded-xl {{ $item->status === 'ready' ? 'bg-green-900/20 border border-green-800' : ($item->status === 'preparing' ? 'bg-orange-900/20 border border-orange-800' : 'bg-stone-800 border border-stone-700') }}">
                    <div class="h-7 w-7 rounded-full bg-amber-600 flex items-center justify-center font-bold text-sm flex-shrink-0">
                        {{ $item->quantity }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-sm">{{ $item->menuItem?->name ?? '?' }}</div>
                        @if($item->special_instructions)
                        <div class="text-xs text-amber-400 mt-0.5">⚠️ {{ $item->special_instructions }}</div>
                        @endif
                        @if($item->modifiers)
                        <div class="text-xs text-stone-400 mt-0.5">{{ implode(', ', (array)$item->modifiers) }}</div>
                        @endif
                    </div>
                    <button wire:click="updateItemStatus({{ $item->id }}, '{{ $item->status === 'ready' ? 'preparing' : 'ready' }}')"
                            class="flex-shrink-0 text-lg transition-opacity {{ $item->status === 'ready' ? 'opacity-100' : 'opacity-40 hover:opacity-70' }}">
                        ✅
                    </button>
                </div>
                @endforeach

                @if($order->notes)
                <div class="mt-3 p-2.5 bg-amber-900/20 border border-amber-800 rounded-xl">
                    <div class="text-xs font-semibold text-amber-400 mb-1">Order Notes:</div>
                    <div class="text-xs text-amber-300">{{ $order->notes }}</div>
                </div>
                @endif
            </div>

            {{-- Action Footer --}}
            <div class="border-t border-stone-800 p-3 flex gap-2">
                @if($order->status === 'pending')
                <button wire:click="updateOrderStatus({{ $order->id }}, 'preparing')"
                        class="flex-1 py-2.5 bg-orange-600 hover:bg-orange-500 text-white font-semibold rounded-xl text-sm transition-colors">
                    🍳 Start Cooking
                </button>
                @elseif($order->status === 'confirmed')
                <button wire:click="updateOrderStatus({{ $order->id }}, 'preparing')"
                        class="flex-1 py-2.5 bg-orange-600 hover:bg-orange-500 text-white font-semibold rounded-xl text-sm transition-colors">
                    🍳 Start Cooking
                </button>
                @elseif($order->status === 'preparing')
                <button wire:click="markAllItemsReady({{ $order->id }})"
                        class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl text-sm transition-colors">
                    ✅ Mark All Ready
                </button>
                <button wire:click="updateOrderStatus({{ $order->id }}, 'ready')"
                        class="flex-1 py-2.5 bg-green-600 hover:bg-green-500 text-white font-semibold rounded-xl text-sm transition-colors">
                    🔔 Order Ready
                </button>
                @elseif($order->status === 'ready')
                <div class="flex-1 text-center py-2.5 text-green-400 font-semibold text-sm">
                    🔔 Waiting for pickup/serve
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
