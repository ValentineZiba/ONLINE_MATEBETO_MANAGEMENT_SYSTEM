@once
<style>
@media print {
    body > *:not(#pos-receipt-print) { display: none !important; }
    #pos-receipt-print { display: block !important; position: fixed; inset: 0; background: white; color: black; padding: 20px; font-family: monospace; font-size: 12px; }
    #pos-receipt-print * { color: black !important; }
}
#pos-receipt-print { display: none; }
</style>
@endonce

{{-- Hidden print receipt element --}}
@if($orderComplete && $lastOrderNumber)
<div id="pos-receipt-print">
    <div style="text-align:center; margin-bottom:12px;">
        <div style="font-size:18px; font-weight:bold;">🍽️ MATEBETO</div>
        <div>Restaurant Receipt</div>
        <div>{{ now()->format('M d, Y h:i A') }}</div>
        <div style="border-top:1px dashed #000;margin:8px 0;"></div>
        <div><strong>Order #{{ $lastOrderNumber }}</strong></div>
        <div>Customer: {{ $receiptCustomer }}</div>
        <div>Payment: {{ strtoupper(str_replace('_',' ',$receiptPayment)) }}</div>
        <div style="border-top:1px dashed #000;margin:8px 0;"></div>
    </div>
    @foreach($receiptItems as $item)
    <div style="display:flex;justify-content:space-between;">
        <span>{{ $item['qty'] }}x {{ $item['name'] }}</span>
        <span>K {{ number_format($item['price'] * $item['qty'], 0) }}</span>
    </div>
    @endforeach
    <div style="border-top:1px dashed #000;margin:8px 0;"></div>
    <div style="display:flex;justify-content:space-between;"><span>Subtotal</span><span>K {{ number_format($receiptSubtotal, 0) }}</span></div>
    <div style="display:flex;justify-content:space-between;"><span>Tax (16%)</span><span>K {{ number_format($receiptTax, 0) }}</span></div>
    <div style="display:flex;justify-content:space-between;font-weight:bold;font-size:14px;border-top:1px dashed #000;padding-top:6px;margin-top:4px;"><span>TOTAL</span><span>K {{ number_format($receiptTotal, 0) }}</span></div>
    <div style="text-align:center;margin-top:16px;">Thank you for dining with us!</div>
</div>
@endif

<div class="flex flex-col lg:flex-row h-screen bg-stone-900 text-white overflow-hidden" x-data="{ posCart: false }">
    {{-- Left: Menu Browser --}}
    <div class="flex flex-col flex-1 lg:w-3/5 border-b lg:border-b-0 lg:border-r border-stone-800 min-h-0">
        {{-- Search + Category Filter --}}
        <div class="p-4 bg-stone-800 space-y-3">
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                <input wire:model.live.debounce.200ms="search" type="text" placeholder="Search menu items..." class="w-full pl-10 pr-4 py-2.5 bg-stone-700 border border-stone-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm placeholder-stone-400">
            </div>
            <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-hide">
                <button wire:click="$set('selectedCategory', null)"
                        class="flex-shrink-0 px-4 py-1.5 rounded-xl text-sm font-medium transition-colors {{ !$selectedCategory ? 'bg-amber-600 text-white' : 'bg-stone-700 text-stone-300 hover:bg-stone-600' }}">
                    All
                </button>
                @foreach($categories as $cat)
                <button wire:click="$set('selectedCategory', {{ $cat->id }})"
                        class="flex-shrink-0 px-4 py-1.5 rounded-xl text-sm font-medium transition-colors {{ $selectedCategory == $cat->id ? 'bg-amber-600 text-white' : 'bg-stone-700 text-stone-300 hover:bg-stone-600' }}">
                    {{ $cat->icon }} {{ $cat->name }}
                </button>
                @endforeach
            </div>
        </div>

        {{-- Mobile: View Cart FAB --}}
        <div class="lg:hidden px-4 py-2">
            <button @click="posCart = true"
                    class="w-full py-3 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-2xl flex items-center justify-center gap-2 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                View Cart
                @if(!empty($cart))
                <span class="bg-white text-amber-600 text-xs font-bold px-2 py-0.5 rounded-full">{{ count($cart) }}</span>
                @endif
            </button>
        </div>

        {{-- Menu Items Grid --}}
        <div class="flex-1 overflow-y-auto p-4">
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
                @forelse($menuItems as $item)
                <button wire:click="addToCart({{ $item->id }})"
                        class="text-left bg-stone-800 hover:bg-stone-700 border border-stone-700 hover:border-amber-500 rounded-2xl p-4 transition-all group">
                    <div class="text-2xl mb-2">{{ $item->category?->icon ?? '🍽️' }}</div>
                    <div class="font-semibold text-sm text-white group-hover:text-amber-400 transition-colors line-clamp-2">{{ $item->name }}</div>
                    <div class="text-amber-400 font-bold mt-2">K {{ number_format($item->effective_price, 0) }}</div>
                    <div class="text-xs text-stone-400 mt-1">{{ $item->preparation_time }}min</div>
                </button>
                @empty
                <div class="col-span-3 text-center py-12 text-stone-500">No items found</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Right: Cart & Checkout --}}
    <div class="flex flex-col lg:w-2/5 w-full bg-stone-900
                fixed inset-0 z-30 lg:static lg:z-auto transition-transform duration-300
                lg:translate-x-0"
         :class="posCart ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'">
        {{-- Cart Header (mobile close button) --}}
        <div class="flex items-center justify-between px-4 pt-3 lg:hidden">
            <span class="text-sm font-semibold text-stone-300">Cart</span>
            <button @click="posCart = false" class="p-2 rounded-xl bg-stone-800 text-stone-400 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        {{-- Order Info --}}
        <div class="p-4 border-b border-stone-800 flex gap-3">
            <select wire:model.live="orderType" class="flex-1 px-3 py-2.5 bg-stone-800 border border-stone-700 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                <option value="dine_in">🍽️ Dine In</option>
                <option value="takeaway">📦 Takeaway</option>
            </select>
            @if($orderType === 'dine_in')
            <select wire:model="tableId" class="flex-1 px-3 py-2.5 bg-stone-800 border border-stone-700 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                <option value="">Select table</option>
                @foreach($tables as $t)
                <option value="{{ $t->id }}">{{ $t->number }} ({{ $t->capacity }}p)</option>
                @endforeach
            </select>
            @endif
        </div>

        {{-- Cart Items --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-2">
            @forelse($cart as $key => $item)
            <div class="bg-stone-800 rounded-2xl p-4 flex items-center gap-3">
                <div class="flex-1 min-w-0">
                    <div class="font-medium text-sm truncate">{{ $item['name'] }}</div>
                    <div class="text-amber-400 text-sm font-semibold">K {{ number_format($item['price'] * $item['qty'], 0) }}</div>
                </div>
                <div class="flex items-center gap-2">
                    <button wire:click="decrementItem('{{ $key }}')" class="h-7 w-7 rounded-full bg-stone-700 hover:bg-stone-600 text-white text-lg flex items-center justify-center transition-colors">−</button>
                    <span class="text-white font-bold w-5 text-center">{{ $item['qty'] }}</span>
                    <button wire:click="incrementItem('{{ $key }}')" class="h-7 w-7 rounded-full bg-stone-700 hover:bg-stone-600 text-white text-lg flex items-center justify-center transition-colors">+</button>
                    <button wire:click="removeItem('{{ $key }}')" class="h-7 w-7 rounded-full bg-red-900/50 hover:bg-red-800 text-red-400 text-sm flex items-center justify-center transition-colors">✕</button>
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center h-full py-12 text-stone-500">
                <div class="text-5xl mb-3">🛒</div>
                <div class="text-sm">Cart is empty</div>
                <div class="text-xs mt-1">Select items from the menu</div>
            </div>
            @endforelse
        </div>

        {{-- Notes --}}
        @if(!empty($cart))
        <div class="px-4 pb-2">
            <input wire:model="notes" type="text" placeholder="Order notes..." class="w-full px-3 py-2 bg-stone-800 border border-stone-700 rounded-xl text-sm text-white placeholder-stone-500 focus:outline-none focus:ring-2 focus:ring-amber-500">
        </div>
        @endif

        {{-- Payment --}}
        <div class="border-t border-stone-800 p-4 space-y-4">
            {{-- Totals --}}
            <div class="space-y-1 text-sm">
                <div class="flex justify-between text-stone-400">
                    <span>Subtotal</span><span>K {{ number_format($subtotal, 0) }}</span>
                </div>
                <div class="flex justify-between text-stone-400">
                    <span>Tax (16%)</span><span>K {{ number_format($tax, 0) }}</span>
                </div>
                <div class="flex justify-between text-white font-bold text-xl pt-2 border-t border-stone-700">
                    <span>Total</span><span class="text-amber-400">K {{ number_format($total, 0) }}</span>
                </div>
            </div>

            {{-- Payment Method --}}
            @php $posPayments = [
                'airtel_money'  => ['logo'=>'airtel-money',  'label'=>'Airtel'],
                'mtn_momo'      => ['logo'=>'mtn-money',     'label'=>'MTN'],
                'zamtel_kwacha' => ['logo'=>'zamtel-kwacha', 'label'=>'Zamtel'],
                'zampay'        => ['logo'=>'zampay',        'label'=>'ZamPay'],
                'card'          => ['logo'=>'card',          'label'=>'Card'],
                'cash'          => ['logo'=>'cash',          'label'=>'Cash'],
            ]; @endphp
            <div class="flex flex-wrap gap-3 justify-center">
                @foreach($posPayments as $pm => $opt)
                <button wire:click="$set('paymentMethod','{{ $pm }}')"
                        class="flex flex-col items-center gap-1 group">
                    <div class="relative w-12 h-12 rounded-full overflow-hidden transition-all duration-200
                        {{ $paymentMethod === $pm
                            ? 'ring-[3px] ring-amber-400 shadow-lg shadow-amber-900/40 scale-110'
                            : 'ring-2 ring-stone-600 hover:ring-amber-500 opacity-75 hover:opacity-100 hover:scale-105' }}">
                        <img src="{{ asset('images/payments/' . $opt['logo'] . '.svg') }}"
                             alt="{{ $opt['label'] }}" class="w-full h-full object-cover">
                        @if($paymentMethod === $pm)
                        <div class="absolute inset-0 bg-black/10 flex items-end justify-end p-0.5">
                            <div class="w-3.5 h-3.5 bg-amber-400 rounded-full flex items-center justify-center">
                                <svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </div>
                        </div>
                        @endif
                    </div>
                    <span class="text-xs {{ $paymentMethod === $pm ? 'text-amber-400 font-semibold' : 'text-stone-400' }}">
                        {{ $opt['label'] }}
                    </span>
                </button>
                @endforeach
            </div>

            {{-- Customer Name --}}
            <input wire:model="customerName" type="text" placeholder="Customer name (optional)" class="w-full px-3 py-2.5 bg-stone-800 border border-stone-700 rounded-xl text-sm text-white placeholder-stone-500 focus:outline-none focus:ring-2 focus:ring-amber-500">

            @if($successMessage)
            <div class="p-3 bg-green-900/40 border border-green-700 rounded-xl text-green-400 text-sm font-medium flex items-center justify-between">
                <span>✅ {{ $successMessage }}</span>
                <button onclick="window.print()" class="text-xs bg-green-700 hover:bg-green-600 text-white px-3 py-1.5 rounded-lg transition-colors font-medium ml-3 flex-shrink-0">
                    🖨️ Print Receipt
                </button>
            </div>
            @endif

            {{-- Place Order --}}
            <button wire:click="placeOrder"
                    @if(empty($cart)) disabled @endif
                    class="w-full py-4 bg-amber-600 hover:bg-amber-500 disabled:opacity-40 disabled:cursor-not-allowed text-white font-bold text-lg rounded-2xl transition-colors">
                Place Order
            </button>
        </div>
    </div>
</div>
