<div class="space-y-5">

    {{-- Stats bar --}}
    <div class="flex items-center gap-3">
        <div class="flex items-center gap-2 px-4 py-2 rounded-2xl bg-stone-900/60 border border-stone-800/60">
            <span class="h-2 w-2 rounded-full bg-red-500 animate-pulse flex-shrink-0"></span>
            <span class="text-stone-300 text-sm font-semibold tabular-nums">{{ $stats['pending'] }}</span>
            <span class="text-stone-500 text-xs">new</span>
        </div>
        <div class="flex items-center gap-2 px-4 py-2 rounded-2xl bg-stone-900/60 border border-stone-800/60">
            <span class="h-2 w-2 rounded-full bg-amber-400 animate-pulse flex-shrink-0"></span>
            <span class="text-stone-300 text-sm font-semibold tabular-nums">{{ $stats['preparing'] }}</span>
            <span class="text-stone-500 text-xs">mixing</span>
        </div>
        <div class="flex items-center gap-2 px-4 py-2 rounded-2xl bg-stone-900/60 border border-stone-800/60">
            <span class="h-2 w-2 rounded-full bg-green-400 flex-shrink-0"></span>
            <span class="text-stone-300 text-sm font-semibold tabular-nums">{{ $stats['ready'] }}</span>
            <span class="text-stone-500 text-xs">ready</span>
        </div>
        <div class="ml-auto flex items-center gap-1.5 text-xs text-stone-600">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Refreshes every 15s
        </div>
    </div>

    @if($orders->isEmpty())
    <div class="flex items-center justify-center h-[65vh]">
        <div class="text-center">
            <div class="w-28 h-28 rounded-3xl bg-stone-900/60 border border-stone-700/40
                        flex items-center justify-center text-5xl mx-auto mb-5">🍹</div>
            <h2 class="text-2xl font-bold text-stone-300 mb-2">All drinks served!</h2>
            <p class="text-stone-500">No active drink orders in the queue</p>
        </div>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach($orders as $order)
        @php
            $age         = $order->created_at->diffInMinutes(now());
            $allBarReady = $order->items->every(fn($i) => $i->status === 'ready');
            $isDone      = $order->status === 'ready' || $allBarReady;

            $borderColor = $isDone
                ? 'border-green-600/60'
                : ($age > 12 ? 'border-red-600/70' : ($age > 6 ? 'border-amber-500/60' : 'border-stone-700/50'));

            $headerBg = $isDone
                ? 'bg-green-900/30'
                : ($order->status === 'preparing' ? 'bg-amber-900/20' : 'bg-stone-800/50');
        @endphp
        <div class="rounded-2xl border {{ $borderColor }} overflow-hidden flex flex-col"
             style="background: rgba(28,25,23,0.75);">

            {{-- Card header --}}
            <div class="{{ $headerBg }} px-4 py-3 border-b border-stone-800/60 flex items-center justify-between">
                <div>
                    <div class="font-bold text-base text-white">#{{ $order->order_number }}</div>
                    <div class="text-xs text-stone-500 mt-0.5">
                        <span class="{{ $age > 10 && !$isDone ? 'text-red-400 font-semibold' : '' }}">{{ $age }}m</span>
                        <span class="mx-1">·</span>
                        @if($order->table && $order->order_type === 'dine_in')
                            🪑 {{ $order->table->number }}
                        @else
                            {{ ucfirst(str_replace('_', ' ', $order->order_type)) }}
                        @endif
                        @if($order->customer_name)
                        <span class="mx-1">·</span>{{ $order->customer_name }}
                        @endif
                    </div>
                </div>
                <div class="text-right">
                    @php
                        $badge = [
                            'pending'   => 'bg-red-500/20 text-red-300 border border-red-700/40',
                            'confirmed' => 'bg-blue-500/20 text-blue-300 border border-blue-700/40',
                            'preparing' => 'bg-amber-500/20 text-amber-300 border border-amber-600/40',
                            'ready'     => 'bg-green-500/20 text-green-300 border border-green-700/40',
                        ][$order->status] ?? 'bg-stone-700 text-stone-300';
                    @endphp
                    <span class="text-xs px-2.5 py-1 rounded-full font-semibold {{ $badge }}">
                        {{ strtoupper($order->status) }}
                    </span>
                    @if($age > 10 && !$isDone)
                    <div class="text-red-400 text-[10px] font-bold mt-1 text-right">⚠ LATE</div>
                    @endif
                </div>
            </div>

            {{-- Items --}}
            <div class="p-3 flex-1 space-y-1.5">
                @foreach($order->items as $item)
                <div class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl border transition-colors
                    {{ $item->status === 'ready'
                        ? 'bg-green-900/20 border-green-700/30'
                        : 'bg-stone-800/50 border-stone-700/40 hover:bg-stone-800/80' }}">
                    <div class="h-6 w-6 rounded-lg bg-amber-700 flex items-center justify-center
                                font-bold text-xs flex-shrink-0 text-white">
                        {{ $item->quantity }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-sm text-stone-100 {{ $item->status === 'ready' ? 'line-through text-stone-500' : '' }}">
                            {{ $item->menuItem?->name ?? '?' }}
                        </div>
                        @if($item->special_instructions)
                        <div class="text-xs text-amber-400 mt-0.5 truncate">{{ $item->special_instructions }}</div>
                        @endif
                    </div>
                    <button wire:click="updateItemStatus({{ $item->id }}, '{{ $item->status === 'ready' ? 'preparing' : 'ready' }}')"
                            class="flex-shrink-0 transition-all text-base
                                   {{ $item->status === 'ready' ? 'opacity-100 scale-110' : 'opacity-30 hover:opacity-70' }}">
                        ✅
                    </button>
                </div>
                @endforeach

                @if($order->notes)
                <div class="mt-2 p-2.5 rounded-xl bg-amber-900/15 border border-amber-800/40">
                    <div class="text-xs font-semibold text-amber-500 mb-0.5">Note</div>
                    <div class="text-xs text-amber-300/80">{{ $order->notes }}</div>
                </div>
                @endif
            </div>

            {{-- Action footer --}}
            <div class="border-t border-stone-800/60 p-3">
                @if(in_array($order->status, ['pending', 'confirmed']))
                <button wire:click="updateOrderStatus({{ $order->id }}, 'preparing')"
                        class="w-full py-2.5 bg-amber-600 hover:bg-amber-500 active:bg-amber-700
                               text-white font-semibold rounded-xl text-sm transition-all
                               shadow-md shadow-amber-900/20 hover:-translate-y-px">
                    🍸 Start Mixing
                </button>
                @elseif($order->status === 'preparing')
                <button wire:click="markAllBarItemsReady({{ $order->id }})"
                        class="w-full py-2.5 bg-green-600 hover:bg-green-500 active:bg-green-700
                               text-white font-semibold rounded-xl text-sm transition-all
                               shadow-md shadow-green-900/20 hover:-translate-y-px">
                    🔔 All Drinks Ready
                </button>
                @else
                <div class="w-full text-center py-2.5 text-green-400 font-semibold text-sm">
                    ✓ Waiting for pickup
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>
