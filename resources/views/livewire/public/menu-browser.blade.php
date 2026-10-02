<div>
    {{-- QR Table Banner --}}
    @if($fromQr && $qrTableNumber)
    <div class="bg-amber-600 text-white text-center py-2.5 px-4 text-sm font-medium flex items-center justify-center gap-2 sticky top-16 z-40">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h7v7H3V3zm11 0h7v7h-7V3zM3 14h7v7H3v-7zm14 3h-3v4h3v-4zm0-3h-1v1h1v-1zm-3 0h-1v1h1v-1z"/></svg>
        Ordering for <strong>Table {{ $qrTableNumber }}</strong> — your order will be delivered to your table
        <button wire:click="$set('fromQr', false)" class="ml-4 text-amber-200 hover:text-white text-xs underline">Not my table?</button>
    </div>
    @endif

    {{-- Order Success Banner --}}
    @if($orderPlaced)
    <div class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4 animate-fade-in">
        <div class="bg-white rounded-3xl p-8 max-w-md w-full text-center shadow-2xl animate-pop-in">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h2 class="font-display text-2xl font-bold text-stone-800 mb-2">Order Placed!</h2>
            <p class="text-stone-500 mb-4">Your order has been received and is being processed.</p>
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
                <div class="text-xs text-amber-700 uppercase tracking-wide mb-1">Order Number</div>
                <div class="text-2xl font-bold text-amber-700">{{ $orderNumber }}</div>
            </div>
            <a href="{{ route('order.track', ['orderNumber' => $orderNumber]) }}" class="block w-full py-3 bg-amber-600 hover:bg-amber-500 text-white font-semibold rounded-xl transition-colors mb-3">Track My Order</a>
            <button wire:click="$set('orderPlaced', false)" class="w-full py-3 border border-stone-200 hover:bg-stone-50 text-stone-600 font-medium rounded-xl transition-colors">Continue Shopping</button>
        </div>
    </div>
    @endif

    {{-- Page Header --}}
    <div class="bg-stone-900 py-16 text-center">
        <h1 class="font-display text-4xl md:text-5xl font-bold text-white mb-3 animate-fade-in-up">Our Menu</h1>
        <p class="text-stone-400 max-w-xl mx-auto animate-fade-in-up stagger-1">Discover our carefully crafted selection of Zambian and international dishes</p>
    </div>

    {{-- Featured Dishes Carousel --}}
    @if($featuredItems->isNotEmpty())
    <div class="bg-stone-900 pb-14 -mt-1">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-2 mb-4">
                <span class="text-amber-500 text-lg">⭐</span>
                <h2 class="font-display text-white text-lg font-bold tracking-tight">Chef's Picks</h2>
            </div>
            <div class="flex gap-4 overflow-x-auto pb-2 snap-x snap-mandatory scrollbar-hide">
                @foreach($featuredItems as $item)
                <div class="group relative flex-shrink-0 w-64 snap-start bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 animate-fade-in-up stagger-{{ ($loop->iteration - 1) % 6 + 1 }}">
                    <div class="relative overflow-hidden" style="height:160px">
                        @if($item->image)
                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" style="width:100%;height:160px;object-fit:cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                        @else
                        <div class="w-full h-full bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center" style="height:160px">
                            <span class="text-4xl">🍽️</span>
                        </div>
                        @endif
                        <div class="absolute top-2.5 left-2.5 px-2.5 py-1 bg-amber-500 text-white text-[11px] font-bold rounded-full shadow uppercase tracking-wide">⭐ Featured</div>
                        <div class="absolute top-2.5 right-2.5 px-2.5 py-1 bg-stone-900/80 backdrop-blur-sm text-white text-xs font-bold rounded-full shadow">K {{ number_format($item->effective_price, 0) }}</div>
                    </div>
                    <div class="p-3.5 flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <div class="font-bold text-stone-800 text-sm truncate">{{ $item->name }}</div>
                            <div class="text-amber-600 text-[11px] uppercase tracking-wide font-semibold">{{ $item->category->name }}</div>
                        </div>
                        <button wire:click="addToCart({{ $item->id }})" wire:loading.attr="disabled"
                                class="flex-shrink-0 w-9 h-9 flex items-center justify-center bg-amber-600 hover:bg-amber-500 active:bg-amber-700 text-white rounded-xl transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col lg:flex-row gap-8">

            {{-- Left: Filters + Menu --}}
            <div class="flex-1">
                {{-- Search + Filters --}}
                <div class="flex flex-col sm:flex-row gap-4 mb-6">
                    <div class="relative flex-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search dishes..." class="w-full pl-12 pr-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white">
                    </div>
                    <button wire:click="$set('cartOpen', true)" class="flex items-center gap-2 px-5 py-3 bg-amber-600 hover:bg-amber-500 text-white rounded-xl font-medium transition-colors relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Cart ({{ $cartCount }})
                        @if($cartCount > 0)
                        <span wire:key="cart-badge-{{ $cartCount }}" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold animate-badge-bounce">{{ $cartCount }}</span>
                        @endif
                    </button>
                </div>

                {{-- Category Tabs --}}
                <div class="flex gap-2 overflow-x-auto pb-2 mb-6 scrollbar-hide">
                    <button wire:click="$set('category', '')"
                            class="flex-shrink-0 px-4 py-2 rounded-full text-sm font-medium transition-colors {{ empty($category) ? 'bg-amber-600 text-white' : 'bg-stone-100 text-stone-600 hover:bg-stone-200' }}">
                        All
                    </button>
                    @foreach($categories as $cat)
                    <button wire:click="$set('category', '{{ $cat->slug }}')"
                            class="flex-shrink-0 flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium transition-colors {{ $category === $cat->slug ? 'bg-amber-600 text-white' : 'bg-stone-100 text-stone-600 hover:bg-stone-200' }}">
                        <span>{{ $cat->icon }}</span> {{ $cat->name }}
                    </button>
                    @endforeach
                </div>

                {{-- Menu Items Grid --}}
                @if($items->isEmpty())
                <div class="text-center py-20 text-stone-400">
                    <div class="text-6xl mb-4">🍽️</div>
                    <h3 class="text-xl font-semibold text-stone-600 mb-2">No items found</h3>
                    <p class="text-sm">Try a different search or category</p>
                </div>
                @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($items as $item)
                    <div wire:key="menu-item-{{ $item->id }}" class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-stone-100 hover:-translate-y-1 animate-fade-in-up stagger-{{ ($loop->iteration - 1) % 6 + 1 }}">
                        {{-- Image area --}}
                        <div class="relative overflow-hidden" style="height:220px">
                            @if($item->image)
                                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                     style="width:100%;height:220px;object-fit:cover">
                                {{-- Soft gradient so bottom badges are readable --}}
                                <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent"></div>
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-amber-50 via-orange-50 to-stone-100 flex flex-col items-center justify-center relative overflow-hidden" style="height:220px">
                                    <div class="absolute top-0 right-0 w-32 h-32 bg-amber-100 rounded-full -translate-y-10 translate-x-10 opacity-50"></div>
                                    <div class="absolute bottom-0 left-0 w-28 h-28 bg-orange-100 rounded-full translate-y-10 -translate-x-10 opacity-50"></div>
                                    <div class="relative w-16 h-16 bg-white rounded-2xl shadow flex items-center justify-center mb-3 border border-amber-100">
                                        <svg class="w-8 h-8 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                    <span class="relative text-xs font-bold text-amber-600/80 uppercase tracking-widest">{{ $item->category->name }}</span>
                                    <span class="relative text-xs text-stone-400 mt-1">No photo yet</span>
                                </div>
                            @endif
                            {{-- Price badge top-right --}}
                            <div class="absolute top-3 right-3 px-3 py-1 bg-stone-900/80 backdrop-blur-sm text-white text-sm font-bold rounded-full shadow">
                                K {{ number_format($item->effective_price, 0) }}
                            </div>
                            {{-- Diet/promo badges top-left --}}
                            <div class="absolute top-3 left-3 flex gap-1 flex-wrap">
                                @if($item->is_vegetarian)<span class="px-2 py-0.5 bg-green-500 text-white text-xs rounded-full font-semibold shadow">Veg</span>@endif
                                @if($item->is_vegan)<span class="px-2 py-0.5 bg-emerald-600 text-white text-xs rounded-full font-semibold shadow">Vegan</span>@endif
                                @if($item->is_spicy)<span class="px-2 py-0.5 bg-red-500 text-white text-xs rounded-full font-semibold shadow">🌶 Spicy</span>@endif
                                @if($item->is_gluten_free)<span class="px-2 py-0.5 bg-blue-500 text-white text-xs rounded-full font-semibold shadow">GF</span>@endif
                                @if($item->discount_price)<span class="px-2 py-0.5 bg-amber-500 text-white text-xs rounded-full font-semibold shadow">Sale</span>@endif
                            </div>
                        </div>
                        {{-- Card body --}}
                        <div class="p-4">
                            <div class="text-amber-600 text-xs uppercase tracking-wide font-semibold mb-1">{{ $item->category->name }}</div>
                            <h3 class="font-bold text-stone-800 text-base mb-1 leading-tight">{{ $item->name }}</h3>
                            <p class="text-stone-400 text-xs leading-relaxed mb-3 line-clamp-2">{{ $item->description }}</p>
                            <div class="flex items-center justify-between gap-2">
                                <div>
                                    <div class="font-bold text-stone-800 text-lg leading-none">K {{ number_format($item->effective_price, 0) }}</div>
                                    @if($item->discount_price)
                                    <div class="text-xs text-stone-400 line-through leading-none mt-0.5">K {{ number_format($item->price, 0) }}</div>
                                    @endif
                                    <div class="flex items-center gap-2 text-xs text-stone-400 mt-1">
                                        @if($item->preparation_time)<span>⏱ {{ $item->preparation_time }}min</span>@endif
                                        @if($item->calories)<span>· {{ $item->calories }} cal</span>@endif
                                    </div>
                                </div>
                                <button wire:click="addToCart({{ $item->id }})" wire:loading.attr="disabled"
                                        class="flex items-center gap-1.5 px-4 py-2.5 bg-amber-600 hover:bg-amber-500 active:bg-amber-700 text-white text-sm font-bold rounded-xl transition-colors flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    Add
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Cart Sidebar (Desktop) --}}
            <div class="lg:w-96 lg:block" @if(!$cartOpen) style="display:none" @endif>
                {{-- Desktop: always visible on LG; mobile handled by cartOpen --}}
            </div>
        </div>
    </div>

    {{-- Cart Drawer (Livewire-powered) --}}
    @if($cartOpen)
    <div class="fixed inset-0 z-50 flex" x-data>
        <div class="absolute inset-0 bg-black/50 animate-fade-in" wire:click="$set('cartOpen', false)"></div>
        <div class="absolute right-0 top-0 h-full w-full max-w-md bg-white shadow-2xl flex flex-col animate-slide-in-right">
            <div class="flex items-center justify-between p-6 border-b">
                <h2 class="font-display text-xl font-bold text-stone-800">Your Cart ({{ $cartCount }})</h2>
                <button wire:click="$set('cartOpen', false)" class="p-2 rounded-xl hover:bg-stone-100 transition-colors text-stone-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-6 space-y-4">
                @forelse($cart as $itemId => $item)
                <div class="flex items-center gap-4 bg-stone-50 rounded-xl p-3">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center text-xl flex-shrink-0">🍽️</div>
                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-stone-800 text-sm truncate">{{ $item['name'] }}</div>
                        <div class="text-amber-600 text-xs font-medium">K {{ number_format($item['price'], 2) }}</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button wire:click="decrementItem({{ $itemId }})" class="w-7 h-7 flex items-center justify-center bg-stone-200 hover:bg-stone-300 rounded-lg text-stone-600 font-bold transition-colors">−</button>
                        <span class="w-6 text-center font-semibold text-sm">{{ $item['quantity'] }}</span>
                        <button wire:click="incrementItem({{ $itemId }})" class="w-7 h-7 flex items-center justify-center bg-amber-100 hover:bg-amber-200 rounded-lg text-amber-700 font-bold transition-colors">+</button>
                    </div>
                    <div class="text-stone-700 font-semibold text-sm w-20 text-right">K {{ number_format($item['subtotal'], 0) }}</div>
                    <button wire:click="removeFromCart({{ $itemId }})" class="text-stone-300 hover:text-red-400 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
                @empty
                <div class="text-center py-16 text-stone-400">
                    <div class="text-5xl mb-4">🛒</div>
                    <p class="font-medium text-stone-500">Your cart is empty</p>
                    <p class="text-sm mt-1">Add items to get started</p>
                </div>
                @endforelse
            </div>

            @if(count($cart) > 0)
            <div class="p-6 border-t bg-stone-50 space-y-4">
                {{-- Coupon --}}
                <div class="flex gap-2">
                    <input wire:model="couponCode" type="text" placeholder="Coupon code" class="flex-1 px-3 py-2 border border-stone-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white uppercase">
                    <button wire:click="applyCoupon" class="px-4 py-2 bg-stone-800 hover:bg-stone-700 text-white text-sm rounded-lg font-medium transition-colors">Apply</button>
                </div>
                @if($couponMessage)
                <div class="text-xs {{ $couponDiscount > 0 ? 'text-green-600' : 'text-red-500' }}">{{ $couponMessage }}</div>
                @endif

                {{-- Totals --}}
                <div class="space-y-1 text-sm text-stone-600">
                    <div class="flex justify-between"><span>Subtotal</span><span class="font-medium">K {{ number_format($cartTotal, 2) }}</span></div>
                    @if($couponDiscount > 0)<div class="flex justify-between text-green-600"><span>Discount</span><span>−K {{ number_format($couponDiscount, 2) }}</span></div>@endif
                    <div class="flex justify-between"><span>Tax (16%)</span><span>K {{ number_format($cartTotal * 0.16, 2) }}</span></div>
                    <div class="flex justify-between text-lg font-bold text-stone-800 pt-2 border-t border-stone-200">
                        <span>Total</span><span class="text-amber-600">K {{ number_format(($cartTotal * 1.16) - $couponDiscount, 2) }}</span>
                    </div>
                </div>
                <button wire:click="$set('checkoutOpen', true)" class="w-full py-3 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl transition-colors">
                    Proceed to Checkout →
                </button>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Checkout Modal --}}
    @if($checkoutOpen)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center sm:p-4">
        <div class="absolute inset-0 bg-black/60 animate-fade-in" wire:click="$set('checkoutOpen', false)"></div>
        <div class="relative bg-white sm:rounded-3xl rounded-t-3xl shadow-2xl w-full max-w-lg max-h-[92vh] overflow-y-auto animate-pop-in">
            <div class="p-4 sm:p-6 border-b flex items-center justify-between sticky top-0 bg-white sm:rounded-t-3xl rounded-t-3xl">
                <h2 class="font-display text-lg sm:text-xl font-bold text-stone-800">Complete Your Order</h2>
                <button wire:click="$set('checkoutOpen', false)" class="p-2 rounded-xl hover:bg-stone-100 text-stone-500 transition-colors">✕</button>
            </div>
            <div class="p-4 sm:p-6 space-y-5">
                @if(session()->has('errors'))
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-red-700 text-sm">Please fix the errors below.</div>
                @endif

                @if($fromQr && $qrTableNumber)
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 flex items-center gap-3">
                    <div class="w-9 h-9 bg-amber-100 rounded-xl flex items-center justify-center text-xl flex-shrink-0">🪑</div>
                    <div>
                        <div class="text-sm font-semibold text-amber-800">Table {{ $qrTableNumber }}</div>
                        <div class="text-xs text-amber-600">Your order will be served at your table</div>
                    </div>
                </div>
                @endif

                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-2">Order Type</label>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach(['dine_in' => '🍽️ Dine In', 'takeaway' => '📦 Takeaway', 'delivery' => '🚴 Delivery'] as $val => $label)
                        <button wire:click="$set('orderType', '{{ $val }}')"
                                class="py-3 px-2 rounded-xl border-2 text-sm font-medium transition-colors text-center {{ $orderType === $val ? 'border-amber-500 bg-amber-50 text-amber-700' : 'border-stone-200 text-stone-600 hover:border-stone-300' }}">
                            {{ $label }}
                        </button>
                        @endforeach
                    </div>
                    @error('orderType') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-2">Your Name *</label>
                        <input wire:model="customerName" type="text" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="Full name">
                        @error('customerName') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-2">Phone *</label>
                        <input wire:model="customerPhone" type="tel" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="+260...">
                        @error('customerPhone') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                @if($orderType === 'delivery')
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-2">Delivery Address *</label>
                    <textarea wire:model.blur="deliveryAddress" rows="2" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 resize-none" placeholder="Street, area, city..."></textarea>
                    @error('deliveryAddress') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    <p wire:loading wire:target="deliveryAddress" class="text-xs text-stone-400 mt-1">Calculating delivery fee…</p>
                    @if(!$calculatingDeliveryFee && $deliveryFeeMessage)
                    <p class="text-xs mt-1 {{ $deliveryDistanceKm !== null ? 'text-stone-500' : 'text-amber-600' }}" wire:loading.remove wire:target="deliveryAddress">{{ $deliveryFeeMessage }}</p>
                    @endif
                </div>
                @endif

                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-3">Payment Method</label>
                    @php
                    $paymentOptions = [
                        'airtel_money'  => ['logo' => 'airtel-money',  'label' => 'Airtel Money'],
                        'mtn_momo'      => ['logo' => 'mtn-money',     'label' => 'MTN Money'],
                        'zamtel_kwacha' => ['logo' => 'zamtel-kwacha', 'label' => 'Zamtel Kwacha'],
                        'zampay'        => ['logo' => 'zampay',        'label' => 'ZamPay'],
                        'card'          => ['logo' => 'card',          'label' => 'Card'],
                        'cash'          => ['logo' => 'cash',          'label' => 'Cash'],
                        'bank_transfer' => ['logo' => 'bank-transfer', 'label' => 'Bank Transfer'],
                    ];
                    @endphp
                    <div class="flex flex-wrap gap-4 justify-start">
                        @foreach($paymentOptions as $val => $opt)
                        <button type="button" wire:click="$set('paymentMethod','{{ $val }}')"
                                class="flex flex-col items-center gap-1.5 group">
                            <div class="relative w-14 h-14 rounded-full overflow-hidden transition-all duration-200
                                {{ $paymentMethod === $val
                                    ? 'ring-[3px] ring-amber-500 shadow-lg shadow-amber-200 scale-110'
                                    : 'ring-2 ring-stone-200 hover:ring-amber-300 hover:scale-105' }}">
                                <img src="{{ asset('images/payments/' . $opt['logo'] . '.svg') }}"
                                     alt="{{ $opt['label'] }}"
                                     class="w-full h-full object-cover">
                                @if($paymentMethod === $val)
                                <div class="absolute inset-0 bg-black/10 flex items-end justify-end p-0.5">
                                    <div class="w-4 h-4 bg-amber-500 rounded-full flex items-center justify-center shadow">
                                        <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <span class="text-xs text-center leading-tight w-14
                                {{ $paymentMethod === $val ? 'text-amber-600 font-semibold' : 'text-stone-500 group-hover:text-stone-700' }}">
                                {{ $opt['label'] }}
                            </span>
                        </button>
                        @endforeach
                    </div>
                    @error('paymentMethod') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Loyalty Points Redemption --}}
                @auth
                @if($this->availablePoints > 0)
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input wire:model.live="usePoints" type="checkbox" class="h-4 w-4 accent-amber-500 flex-shrink-0">
                        <div class="flex-1">
                            <div class="text-sm font-semibold text-amber-800">
                                ⭐ Use {{ number_format($this->availablePoints) }} loyalty points
                            </div>
                            <div class="text-xs text-amber-600 mt-0.5">
                                Saves K {{ number_format($this->pointsDiscount, 2) }} on this order (10 pts = K1)
                            </div>
                        </div>
                        @if($usePoints)
                        <span class="text-xs font-bold text-green-600 bg-green-100 px-2 py-1 rounded-full">Applied ✓</span>
                        @endif
                    </label>
                </div>
                @endif
                @endauth

                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-2">Special Notes</label>
                    <textarea wire:model="notes" rows="2" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 resize-none" placeholder="Allergies, preferences, special requests..."></textarea>
                </div>

                <div class="bg-stone-50 rounded-xl p-4 space-y-2 text-sm">
                    <div class="flex justify-between text-stone-600"><span>Subtotal</span><span>K {{ number_format($cartTotal, 2) }}</span></div>
                    <div class="flex justify-between text-stone-600"><span>Tax (16%)</span><span>K {{ number_format($cartTotal * 0.16, 2) }}</span></div>
                    @if($orderType === 'delivery')
                    <div class="flex justify-between text-stone-600">
                        <span>Delivery Fee @if($deliveryDistanceKm !== null)<span class="text-xs text-stone-400">({{ $deliveryDistanceKm }} km)</span>@endif</span>
                        <span>K {{ number_format($deliveryFee, 2) }}</span>
                    </div>
                    @endif
                    @if($couponDiscount > 0)<div class="flex justify-between text-green-600"><span>Coupon Discount</span><span>−K {{ number_format($couponDiscount, 2) }}</span></div>@endif
                    @if($usePoints && $this->pointsDiscount > 0)<div class="flex justify-between text-amber-600"><span>⭐ Points Discount</span><span>−K {{ number_format($this->pointsDiscount, 2) }}</span></div>@endif
                    <div class="flex justify-between font-bold text-stone-800 text-base pt-2 border-t border-stone-200">
                        <span>Total</span>
                        <span class="text-amber-600">K {{ number_format(max(0, ($cartTotal * 1.16) + ($orderType === 'delivery' ? $deliveryFee : 0) - $couponDiscount - ($usePoints ? $this->pointsDiscount : 0)), 2) }}</span>
                    </div>
                </div>

                <button wire:click="placeOrder" wire:loading.attr="disabled"
                        class="w-full py-4 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl transition-colors text-lg shadow-lg shadow-amber-900/20">
                    <span wire:loading.remove>Place Order 🎉</span>
                    <span wire:loading>Processing...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
