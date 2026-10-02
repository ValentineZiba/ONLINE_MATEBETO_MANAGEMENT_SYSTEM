<div class="max-w-3xl mx-auto space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-4 animate-fade-in-up">
        <h2 class="text-2xl font-bold text-stone-800">My Orders</h2>
        @auth
        <div class="flex items-center gap-3 bg-amber-50 border border-amber-200 rounded-2xl px-5 py-3">
            <div class="text-2xl">⭐</div>
            <div>
                <div class="text-xl font-bold text-amber-700">{{ number_format(auth()->user()->loyalty_points) }}</div>
                <div class="text-xs text-amber-600">Loyalty Points</div>
            </div>
            @if(auth()->user()->total_points_earned > 0)
            <div class="border-l border-amber-200 pl-3 ml-1">
                <div class="text-sm font-semibold text-stone-600">{{ number_format(auth()->user()->total_points_earned) }}</div>
                <div class="text-xs text-stone-400">Total Earned</div>
            </div>
            @endif
        </div>
        @endauth
    </div>

    @forelse($orders as $order)
    <div wire:key="order-{{ $order->id }}" class="bg-white rounded-2xl shadow-sm border border-stone-100 overflow-hidden animate-fade-in-up stagger-{{ ($loop->iteration - 1) % 6 + 1 }}">
        <div class="px-6 py-4 flex flex-wrap items-center justify-between gap-3 border-b border-stone-50">
            <div>
                <div class="font-semibold text-stone-800">#{{ $order->order_number }}</div>
                <div class="text-xs text-stone-400 mt-0.5">{{ $order->created_at->format('M d, Y • h:i A') }}</div>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs px-3 py-1 rounded-full font-medium
                    {{ match($order->status) {
                        'pending' => 'bg-yellow-100 text-yellow-700',
                        'confirmed' => 'bg-blue-100 text-blue-700',
                        'preparing' => 'bg-orange-100 text-orange-700',
                        'ready' => 'bg-purple-100 text-purple-700',
                        'served', 'completed' => 'bg-green-100 text-green-700',
                        'cancelled' => 'bg-red-100 text-red-600',
                        default => 'bg-stone-100 text-stone-600'
                    } }}">{{ ucfirst($order->status) }}</span>
                <span class="text-xs px-2 py-1 rounded-full font-medium {{ $order->payment_status === 'paid' ? 'bg-green-50 text-green-600' : 'bg-yellow-50 text-yellow-600' }}">
                    {{ ucfirst($order->payment_status) }}
                </span>
            </div>
        </div>
        <div class="px-6 py-4">
            <div class="space-y-2 mb-4">
                @foreach($order->items as $item)
                <div class="flex justify-between text-sm">
                    <span class="text-stone-600">{{ $item->quantity }}× {{ $item->menuItem?->name ?? '?' }}</span>
                    <span class="text-stone-700 font-medium">K {{ number_format($item->subtotal, 0) }}</span>
                </div>
                @endforeach
            </div>
            <div class="flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-stone-50">
                <div class="text-sm text-stone-500">
                    {{ $order->order_type === 'dine_in' ? '🍽️ Dine In' : ($order->order_type === 'takeaway' ? '📦 Takeaway' : '🚴 Delivery') }}
                    @if($order->table) • Table {{ $order->table->number }} @endif
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <div class="font-bold text-lg text-stone-800">K {{ number_format($order->total, 0) }}</div>
                    </div>
                    <button wire:click="viewOrder({{ $order->id }})" class="px-4 py-2 text-sm border border-stone-200 hover:border-amber-300 hover:text-amber-600 text-stone-600 rounded-xl transition-colors">
                        Details
                    </button>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-24">
        <div class="text-6xl mb-4">🛍️</div>
        <h3 class="text-xl font-semibold text-stone-700">No orders yet</h3>
        <p class="text-stone-400 mt-2 mb-6">Your order history will appear here</p>
        <a href="{{ route('menu') }}" class="inline-block px-8 py-3 bg-amber-600 hover:bg-amber-500 text-white font-semibold rounded-xl transition-colors">Browse Menu</a>
    </div>
    @endforelse

    <div class="mt-4">{{ $orders->links() }}</div>

    {{-- Order Detail Modal --}}
    @if($showModal && $selectedOrder)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 animate-fade-in" wire:click="$set('showModal', false)"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto animate-pop-in">
            <div class="p-6 border-b flex items-center justify-between sticky top-0 bg-white rounded-t-3xl">
                <h2 class="font-semibold text-stone-800">Order #{{ $selectedOrder->order_number }}</h2>
                <button wire:click="$set('showModal', false)" class="p-2 hover:bg-stone-100 rounded-xl">✕</button>
            </div>
            <div class="p-6 space-y-5">
                {{-- Status progress --}}
                @php $steps = ['pending','confirmed','preparing','ready','completed'];
                $current = array_search($selectedOrder->status, $steps); @endphp
                <div class="flex items-center gap-1">
                    @foreach($steps as $i => $step)
                    <div class="flex-1 h-1.5 rounded-full {{ $i <= $current ? 'bg-amber-500' : 'bg-stone-100' }}"></div>
                    @endforeach
                </div>

                {{-- Items --}}
                <div class="space-y-2">
                    @foreach($selectedOrder->items as $item)
                    <div class="flex justify-between py-2 border-b border-stone-50 text-sm">
                        <span class="text-stone-700">{{ $item->quantity }}× {{ $item->menuItem?->name }}</span>
                        <span class="font-medium">K {{ number_format($item->subtotal, 0) }}</span>
                    </div>
                    @endforeach
                </div>

                {{-- Totals --}}
                <div class="bg-stone-50 rounded-xl p-4 space-y-2 text-sm">
                    <div class="flex justify-between text-stone-600"><span>Subtotal</span><span>K {{ number_format($selectedOrder->subtotal, 0) }}</span></div>
                    @if($selectedOrder->discount > 0)<div class="flex justify-between text-green-600"><span>Discount</span><span>-K {{ number_format($selectedOrder->discount, 0) }}</span></div>@endif
                    <div class="flex justify-between text-stone-600"><span>Tax (16%)</span><span>K {{ number_format($selectedOrder->tax, 0) }}</span></div>
                    @if($selectedOrder->delivery_fee > 0)<div class="flex justify-between text-stone-600"><span>Delivery</span><span>K {{ number_format($selectedOrder->delivery_fee, 0) }}</span></div>@endif
                    <div class="flex justify-between font-bold text-stone-800 text-base pt-2 border-t border-stone-200"><span>Total</span><span>K {{ number_format($selectedOrder->total, 0) }}</span></div>
                </div>

                <a href="{{ route('invoice.download', $selectedOrder) }}" target="_blank"
                   class="flex items-center justify-center gap-2 w-full py-3 border border-stone-200 hover:bg-stone-50 text-stone-600 font-semibold rounded-xl transition-colors text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Download Invoice
                </a>
                @if(in_array($selectedOrder->status, ['completed','served']))
                <a href="{{ route('menu') }}" class="block w-full text-center py-3 bg-amber-600 hover:bg-amber-500 text-white font-semibold rounded-xl">Reorder</a>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
