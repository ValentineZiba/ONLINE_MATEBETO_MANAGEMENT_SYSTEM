<div>
    <div class="bg-stone-900 py-16 text-center">
        <h1 class="font-display text-4xl md:text-5xl font-bold text-white mb-3 animate-fade-in-up">Track Your Order</h1>
        <p class="text-stone-400 animate-fade-in-up stagger-1">Enter your order number to see real-time status updates</p>
    </div>
    <div class="max-w-2xl mx-auto px-4 py-12">
        {{-- Search Form --}}
        <div class="bg-white rounded-2xl shadow-sm border border-stone-100 p-6 mb-8 animate-fade-in-up stagger-2">
            <div class="flex gap-3">
                <input wire:model="orderNumber" wire:keydown.enter="track" type="text"
                       placeholder="e.g. ORD-ABCD1234"
                       class="flex-1 px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 font-mono uppercase">
                <button wire:click="track" wire:loading.attr="disabled"
                        class="px-6 py-3 bg-amber-600 hover:bg-amber-500 text-white font-semibold rounded-xl transition-colors">
                    <span wire:loading.remove>Track</span>
                    <span wire:loading>...</span>
                </button>
            </div>
            @if($error)
            <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-xl text-red-600 text-sm">{{ $error }}</div>
            @endif
        </div>

        @if($order)
        <div class="space-y-6 animate-fade-in-up">
            {{-- Order Header --}}
            <div class="bg-white rounded-2xl shadow-sm border border-stone-100 p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <div class="text-xs text-stone-400 uppercase tracking-wide mb-1">Order Number</div>
                        <div class="text-2xl font-bold font-mono text-stone-800">{{ $order->order_number }}</div>
                    </div>
                    <span class="px-4 py-2 rounded-full text-sm font-bold
                        {{ match($order->status) {
                            'pending' => 'bg-yellow-100 text-yellow-700',
                            'confirmed' => 'bg-blue-100 text-blue-700',
                            'preparing' => 'bg-orange-100 text-orange-700',
                            'ready' => 'bg-green-100 text-green-700',
                            'served','completed' => 'bg-gray-100 text-gray-700',
                            'cancelled' => 'bg-red-100 text-red-700',
                            default => 'bg-gray-100 text-gray-700',
                        } }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                    <div><div class="text-stone-400 text-xs">Type</div><div class="font-medium text-stone-700 capitalize">{{ str_replace('_', ' ', $order->order_type) }}</div></div>
                    <div><div class="text-stone-400 text-xs">Items</div><div class="font-medium text-stone-700">{{ $order->items->sum('quantity') }}</div></div>
                    <div><div class="text-stone-400 text-xs">Total</div><div class="font-medium text-amber-600">K {{ number_format($order->total, 2) }}</div></div>
                    <div><div class="text-stone-400 text-xs">Placed</div><div class="font-medium text-stone-700">{{ $order->created_at->format('h:i A') }}</div></div>
                </div>
            </div>

            {{-- Progress Tracker --}}
            <div class="bg-white rounded-2xl shadow-sm border border-stone-100 p-6">
                <h3 class="font-semibold text-stone-800 mb-6">Order Progress</h3>
                <div class="relative">
                    <div class="absolute top-5 left-5 right-5 h-0.5 bg-stone-100"></div>
                    <div class="flex justify-between relative">
                        @foreach($this->progressSteps as $step)
                        <div class="flex flex-col items-center gap-2 flex-1">
                            <div class="w-10 h-10 rounded-full border-2 flex items-center justify-center text-lg relative z-10
                                {{ $step['current'] ? 'border-amber-500 bg-amber-500 text-white shadow-lg shadow-amber-500/30 animate-pulse' : ($step['completed'] ? 'border-green-500 bg-green-500 text-white' : 'border-stone-200 bg-white text-stone-300') }}">
                                {{ $step['completed'] && !$step['current'] ? '✓' : $step['icon'] }}
                            </div>
                            <div class="text-xs text-center leading-tight {{ $step['current'] ? 'text-amber-600 font-semibold' : ($step['completed'] ? 'text-green-600 font-medium' : 'text-stone-400') }}">
                                {{ $step['label'] }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @if($order->estimated_minutes && !in_array($order->status, ['completed', 'served', 'cancelled']))
                <div class="mt-6 bg-amber-50 border border-amber-200 rounded-xl p-4 text-center">
                    <div class="text-amber-700 text-sm font-medium">⏱ Estimated time: {{ $order->estimated_minutes }} minutes</div>
                </div>
                @endif
            </div>

            {{-- Order Items --}}
            <div class="bg-white rounded-2xl shadow-sm border border-stone-100 p-6">
                <h3 class="font-semibold text-stone-800 mb-4">Your Items</h3>
                <div class="divide-y divide-stone-50">
                    @foreach($order->items as $item)
                    <div class="flex items-center justify-between py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center text-base flex-shrink-0">🍽️</div>
                            <div>
                                <div class="font-medium text-stone-800 text-sm">{{ $item->menuItem->name }}</div>
                                <div class="text-stone-400 text-xs">K {{ number_format($item->unit_price, 2) }} × {{ $item->quantity }}</div>
                            </div>
                        </div>
                        <div class="font-semibold text-stone-700 text-sm">K {{ number_format($item->subtotal, 2) }}</div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-4 pt-4 border-t border-stone-100 space-y-1 text-sm">
                    <div class="flex justify-between text-stone-500"><span>Subtotal</span><span>K {{ number_format($order->subtotal, 2) }}</span></div>
                    <div class="flex justify-between text-stone-500"><span>Tax</span><span>K {{ number_format($order->tax, 2) }}</span></div>
                    @if($order->delivery_fee > 0)<div class="flex justify-between text-stone-500"><span>Delivery</span><span>K {{ number_format($order->delivery_fee, 2) }}</span></div>@endif
                    <div class="flex justify-between font-bold text-stone-800 text-base pt-2 border-t border-stone-100"><span>Total</span><span class="text-amber-600">K {{ number_format($order->total, 2) }}</span></div>
                </div>
            </div>

            {{-- Download Invoice (logged-in owner or admin) --}}
            @auth
            @if(auth()->id() === $order->user_id || in_array(auth()->user()->role, ['admin','manager']))
            <div class="text-center">
                <a href="{{ route('invoice.download', $order) }}" target="_blank"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-stone-800 hover:bg-stone-700 text-white text-sm font-semibold rounded-xl transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Download Invoice PDF
                </a>
            </div>
            @endif
            @endauth

            <div class="text-center text-stone-400 text-xs">
                <div wire:loading>Checking for updates...</div>
                <div wire:loading.remove>Auto-refreshing every 10 seconds • Last updated: {{ now()->format('h:i:s A') }}</div>
            </div>
        </div>
        @elseif(!$tracking)
        <div class="text-center text-stone-400 py-8">
            <div class="text-6xl mb-4">📦</div>
            <h3 class="text-xl font-semibold text-stone-600 mb-2">Track your order in real-time</h3>
            <p class="text-sm">Enter the order number from your confirmation to see status updates</p>
        </div>
        @endif
    </div>
</div>
