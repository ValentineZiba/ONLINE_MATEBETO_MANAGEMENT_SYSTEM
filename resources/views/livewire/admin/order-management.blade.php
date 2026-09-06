<div class="space-y-6" x-data>
    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">⚠️ {{ session('error') }}</div>
    @endif

    {{-- Header + Filters --}}
    <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
        <div class="flex flex-wrap gap-3 flex-1">
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search orders..." class="pl-10 pr-4 py-2 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 w-52">
            </div>
            <select wire:model.live="statusFilter" class="px-3 py-2 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white">
                <option value="">All Statuses</option>
                @foreach(['pending', 'confirmed', 'preparing', 'ready', 'served', 'completed', 'cancelled'] as $s)
                <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <select wire:model.live="typeFilter" class="px-3 py-2 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white">
                <option value="">All Types</option>
                <option value="dine_in">Dine In</option>
                <option value="takeaway">Takeaway</option>
                <option value="delivery">Delivery</option>
            </select>
        </div>
        <button wire:click="$set('showCreateModal', true)" class="px-5 py-2 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-sm font-semibold transition-colors flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Order
        </button>
    </div>

    {{-- Orders Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-stone-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-stone-50 text-left">
                        <th class="px-4 py-3 text-xs font-semibold text-stone-500 uppercase">Order</th>
                        <th class="px-4 py-3 text-xs font-semibold text-stone-500 uppercase">Customer</th>
                        <th class="px-4 py-3 text-xs font-semibold text-stone-500 uppercase">Total</th>
                        <th class="px-4 py-3 text-xs font-semibold text-stone-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-xs font-semibold text-stone-500 uppercase hidden sm:table-cell">Payment</th>
                        <th class="px-4 py-3 text-xs font-semibold text-stone-500 uppercase hidden md:table-cell">Time</th>
                        <th class="px-4 py-3 text-xs font-semibold text-stone-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-50">
                    @forelse($orders as $order)
                    <tr class="hover:bg-stone-50 transition-colors">
                        {{-- Order # + type badge + table --}}
                        <td class="px-4 py-3">
                            <div class="font-mono text-xs font-semibold text-stone-800">{{ $order->order_number }}</div>
                            <div class="flex items-center gap-1 mt-1">
                                <span class="text-xs px-1.5 py-0.5 rounded-full font-medium
                                    {{ match($order->order_type) { 'dine_in' => 'bg-green-50 text-green-700', 'takeaway' => 'bg-blue-50 text-blue-700', 'delivery' => 'bg-purple-50 text-purple-700', default => 'bg-stone-50 text-stone-600' } }}">
                                    {{ match($order->order_type) { 'dine_in' => '🍽️ Dine In', 'takeaway' => '📦 Takeaway', 'delivery' => '🚴 Delivery', default => $order->order_type } }}
                                </span>
                                @if($order->table)<span class="text-xs text-stone-400">T{{ $order->table->number }}</span>@endif
                            </div>
                        </td>
                        {{-- Customer --}}
                        <td class="px-4 py-3">
                            <div class="text-sm font-medium text-stone-700 truncate max-w-[130px]">{{ $order->customer_name ?? '—' }}</div>
                            <div class="text-xs text-stone-400">{{ $order->customer_phone }}</div>
                        </td>
                        {{-- Total + item count --}}
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="font-semibold text-stone-800 text-sm">K {{ number_format($order->total, 2) }}</div>
                            <div class="text-xs text-stone-400">{{ $order->items->sum('quantity') }} items</div>
                        </td>
                        {{-- Status dropdown --}}
                        <td class="px-4 py-3">
                            <select wire:change="updateStatus({{ $order->id }}, $event.target.value)"
                                    class="text-xs px-2 py-1.5 rounded-lg border font-medium focus:outline-none cursor-pointer w-28
                                    {{ match($order->status) {
                                        'pending'   => 'border-yellow-300 text-yellow-700 bg-yellow-50',
                                        'confirmed' => 'border-blue-300 text-blue-700 bg-blue-50',
                                        'preparing' => 'border-orange-300 text-orange-700 bg-orange-50',
                                        'ready'     => 'border-green-300 text-green-700 bg-green-50',
                                        'served', 'completed' => 'border-stone-300 text-stone-600 bg-stone-50',
                                        'cancelled' => 'border-red-300 text-red-600 bg-red-50',
                                        default     => 'border-stone-200 text-stone-600',
                                    } }}">
                                @foreach($statuses as $s)
                                <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </td>
                        {{-- Payment --}}
                        <td class="px-4 py-3 hidden sm:table-cell">
                            <span class="text-xs px-2 py-1 rounded-full whitespace-nowrap {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </td>
                        {{-- Time --}}
                        <td class="px-4 py-3 text-xs text-stone-400 whitespace-nowrap hidden md:table-cell">{{ $order->created_at->diffForHumans() }}</td>
                        {{-- Actions: icon buttons --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1">
                                {{-- View --}}
                                <button wire:click="viewOrder({{ $order->id }})" title="View order"
                                        class="p-1.5 rounded-lg text-stone-400 hover:text-amber-600 hover:bg-amber-50 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                                {{-- Record Payment (if unpaid) --}}
                                @if($order->payment_status !== 'paid')
                                <button wire:click="openRecordPaymentModal({{ $order->id }})" title="Record payment"
                                        class="p-1.5 rounded-lg text-stone-400 hover:text-green-600 hover:bg-green-50 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </button>
                                @endif
                                {{-- Refund (if paid) --}}
                                @if($order->payment_status === 'paid')
                                <button wire:click="openRefundModal({{ $order->id }})" title="Refund"
                                        class="p-1.5 rounded-lg text-stone-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l-4-4m0 0l4-4m-4 4h11a4 4 0 010 8h-1"/>
                                    </svg>
                                </button>
                                @endif
                                {{-- Assign driver (delivery only) --}}
                                @if($order->order_type === 'delivery')
                                <button wire:click="openDriverModal({{ $order->id }})" title="Assign driver"
                                        class="p-1.5 rounded-lg text-stone-400 hover:text-blue-600 hover:bg-blue-50 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10l2 .001M13 16l2-5h3.5L21 14v2h-2M13 16H9"/>
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-16 text-stone-400">No orders found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-stone-50">{{ $orders->links() }}</div>
    </div>

    {{-- Create Order Modal --}}
    @if($showCreateModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" wire:click="$set('showCreateModal', false)"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b flex items-center justify-between sticky top-0 bg-white rounded-t-3xl z-10">
                <h2 class="font-semibold text-stone-800">Create New Order</h2>
                <button wire:click="$set('showCreateModal', false)" class="p-2 hover:bg-stone-100 rounded-xl text-stone-400">✕</button>
            </div>
            <div class="p-6 space-y-5">

                {{-- Customer Info --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-1.5">Customer Name *</label>
                        <input wire:model="customerName" type="text" placeholder="Walk-in Customer"
                               class="w-full px-4 py-2.5 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                        @error('customerName') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-1.5">Phone</label>
                        <input wire:model="customerPhone" type="tel"
                               class="w-full px-4 py-2.5 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                    </div>
                </div>

                {{-- Order Type + Table --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-1.5">Order Type *</label>
                        <select wire:model.live="orderType" class="w-full px-4 py-2.5 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm bg-white">
                            <option value="dine_in">🍽️ Dine In</option>
                            <option value="takeaway">📦 Takeaway</option>
                            <option value="delivery">🚴 Delivery</option>
                        </select>
                    </div>
                    @if($orderType === 'dine_in')
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-1.5">Table</label>
                        <select wire:model="tableId" class="w-full px-4 py-2.5 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm bg-white">
                            <option value="">No table</option>
                            @foreach($tables as $t)
                            <option value="{{ $t->id }}">{{ $t->number }} ({{ $t->capacity }} seats)</option>
                            @endforeach
                        </select>
                    </div>
                    @else
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-1.5">Payment Method *</label>
                        <select wire:model="paymentMethod" class="w-full px-4 py-2.5 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm bg-white">
                            <option value="cash">💵 Cash</option>
                            <option value="card">💳 Card</option>
                            <option value="airtel_money">📱 Airtel Money</option>
                            <option value="mtn_momo">🟡 MTN Mobile Money</option>
                            <option value="zamtel_kwacha">🟢 Zamtel Kwacha</option>
                            <option value="zampay">💻 ZamPay</option>
                            <option value="bank_transfer">🏦 Bank Transfer</option>
                        </select>
                    </div>
                    @endif
                </div>
                @if($orderType === 'dine_in')
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-1.5">Payment Method *</label>
                    <select wire:model="paymentMethod" class="w-full px-4 py-2.5 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm bg-white">
                        <option value="cash">💵 Cash</option>
                        <option value="card">💳 Card</option>
                        <option value="airtel_money">📱 Airtel Money</option>
                        <option value="mtn_momo">🟡 MTN Mobile Money</option>
                        <option value="zamtel_kwacha">🟢 Zamtel Kwacha</option>
                        <option value="zampay">💻 ZamPay</option>
                        <option value="bank_transfer">🏦 Bank Transfer</option>
                    </select>
                </div>
                @endif
                @if($orderType === 'delivery')
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-1.5">Delivery Address *</label>
                    <textarea wire:model.blur="deliveryAddress" rows="2" placeholder="Full delivery address…"
                              class="w-full px-4 py-2.5 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm resize-none"></textarea>
                    @error('deliveryAddress') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    <p wire:loading wire:target="deliveryAddress" class="text-xs text-stone-400 mt-1">Calculating delivery fee…</p>
                    @if($deliveryFeeMessage)
                    <p class="text-xs mt-1 {{ $deliveryDistanceKm !== null ? 'text-stone-500' : 'text-amber-600' }}" wire:loading.remove wire:target="deliveryAddress">
                        {{ $deliveryFeeMessage }} — fee: K {{ number_format($deliveryFee, 2) }}
                    </p>
                    @endif
                </div>
                @endif

                {{-- Add Items --}}
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-2">Add Items</label>
                    <div class="flex gap-2">
                        <select wire:model="addItemId" class="flex-1 px-3 py-2.5 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm bg-white">
                            <option value="">Select menu item…</option>
                            @foreach($menuItems as $mi)
                            <option value="{{ $mi->id }}">{{ $mi->name }} — K {{ number_format($mi->effective_price, 0) }}</option>
                            @endforeach
                        </select>
                        <input wire:model="addItemQty" type="number" min="1" value="1"
                               class="w-16 px-3 py-2.5 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm text-center">
                        <button wire:click="addItemToNew"
                                class="px-4 py-2.5 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-sm font-semibold transition-colors whitespace-nowrap">
                            + Add
                        </button>
                    </div>
                </div>

                {{-- Items List --}}
                @if(!empty($newItems))
                <div class="border border-stone-100 rounded-xl overflow-hidden">
                    <table class="w-full text-sm">
                        <thead><tr class="bg-stone-50">
                            <th class="text-left px-4 py-2 text-xs text-stone-500">Item</th>
                            <th class="text-center px-4 py-2 text-xs text-stone-500">Qty</th>
                            <th class="text-right px-4 py-2 text-xs text-stone-500">Subtotal</th>
                            <th class="px-4 py-2"></th>
                        </tr></thead>
                        <tbody class="divide-y divide-stone-50">
                            @foreach($newItems as $i => $item)
                            <tr>
                                <td class="px-4 py-3 font-medium text-stone-700">{{ $item['name'] }}</td>
                                <td class="px-4 py-3 text-center text-stone-500">{{ $item['qty'] }}</td>
                                <td class="px-4 py-3 text-right font-medium text-stone-800">K {{ number_format($item['subtotal'], 0) }}</td>
                                <td class="px-4 py-3 text-right">
                                    <button wire:click="removeNewItem({{ $i }})" class="text-red-400 hover:text-red-600 text-xs transition-colors">✕</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @php $sub = collect($newItems)->sum('subtotal'); $tax = $sub * 0.16; $delFee = $orderType === 'delivery' ? $deliveryFee : 0; @endphp
                    <div class="px-4 py-3 bg-stone-50 border-t border-stone-100 space-y-1 text-sm">
                        <div class="flex justify-between text-stone-500"><span>Subtotal</span><span>K {{ number_format($sub, 0) }}</span></div>
                        <div class="flex justify-between text-stone-500"><span>Tax (16%)</span><span>K {{ number_format($tax, 0) }}</span></div>
                        @if($orderType === 'delivery')
                        <div class="flex justify-between text-stone-500"><span>Delivery Fee</span><span>K {{ number_format($delFee, 0) }}</span></div>
                        @endif
                        <div class="flex justify-between font-bold text-stone-800 text-base pt-1 border-t border-stone-200">
                            <span>Total</span><span class="text-amber-600">K {{ number_format($sub + $tax + $delFee, 0) }}</span>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Notes --}}
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-1.5">Notes</label>
                    <textarea wire:model="notes" rows="2" placeholder="Any special instructions…"
                              class="w-full px-4 py-2.5 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm resize-none"></textarea>
                </div>

                {{-- Submit --}}
                <div class="flex gap-3 pt-2">
                    <button wire:click="$set('showCreateModal', false)" class="flex-1 py-3 border border-stone-200 rounded-xl text-sm font-medium text-stone-600 hover:bg-stone-50 transition-colors">Cancel</button>
                    <button wire:click="createOrder" wire:loading.attr="disabled"
                            class="flex-1 py-3 bg-amber-600 hover:bg-amber-500 disabled:opacity-50 text-white rounded-xl text-sm font-semibold transition-colors">
                        <span wire:loading.remove wire:target="createOrder">Create Order</span>
                        <span wire:loading wire:target="createOrder">Creating…</span>
                    </button>
                </div>

            </div>
        </div>
    </div>
    @endif

    {{-- View Order Modal --}}
    {{-- Driver Assignment Modal --}}
    @if($showDriverModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" wire:click="$set('showDriverModal', false)"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-semibold text-stone-800">Assign Delivery Driver</h2>
                <button wire:click="$set('showDriverModal', false)" class="p-2 hover:bg-stone-100 rounded-xl text-stone-400">✕</button>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-1.5">Rider *</label>
                    <select wire:model="selectedRiderId" class="w-full px-4 py-2.5 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm bg-white">
                        <option value="">Select a registered rider…</option>
                        @foreach($activeRiders as $rider)
                        <option value="{{ $rider->id }}">{{ $rider->vehicle_icon }} {{ $rider->name }} — {{ $rider->phone }}{{ $rider->plate_number ? ' (' . $rider->plate_number . ')' : '' }}</option>
                        @endforeach
                    </select>
                    @error('selectedRiderId') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    @if($activeRiders->isEmpty())
                    <p class="mt-1 text-xs text-amber-600">No active riders registered yet. <a href="{{ route('admin.delivery-riders') }}" class="underline">Register one first</a>.</p>
                    @endif
                </div>
                <div class="flex gap-3 pt-2">
                    <button wire:click="$set('showDriverModal', false)" class="flex-1 py-2.5 border border-stone-200 rounded-xl text-sm font-medium text-stone-600 hover:bg-stone-50 transition-colors">Cancel</button>
                    <button wire:click="assignDriver" class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-sm font-semibold transition-colors">Assign Driver</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Record Payment Modal --}}
    @if($showPaymentModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" wire:click="$set('showPaymentModal', false)"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-semibold text-stone-800">Record Payment</h2>
                <button wire:click="$set('showPaymentModal', false)" class="p-2 hover:bg-stone-100 rounded-xl text-stone-400">✕</button>
            </div>
            <div class="space-y-4">
                <p class="text-xs text-stone-500">All methods are currently staff-attested (no live gateway charge yet) — confirm payment with the customer before recording it here.</p>
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-1.5">Method *</label>
                    <select wire:model="recordPaymentGateway" class="w-full px-4 py-2.5 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm bg-white">
                        <option value="cash">💵 Cash</option>
                        <option value="card">💳 Card</option>
                        <option value="airtel_money">📱 Airtel Money</option>
                        <option value="mtn_momo">🟡 MTN Mobile Money</option>
                        <option value="zamtel_kwacha">🟢 Zamtel Kwacha</option>
                        <option value="zampay">💻 ZamPay</option>
                        <option value="bank_transfer">🏦 Bank Transfer</option>
                    </select>
                    @error('recordPaymentGateway') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-1.5">Note (optional)</label>
                    <input wire:model="recordPaymentNote" type="text" class="w-full px-4 py-2.5 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                </div>
                <div class="flex gap-3 pt-2">
                    <button wire:click="$set('showPaymentModal', false)" class="flex-1 py-2.5 border border-stone-200 rounded-xl text-sm font-medium text-stone-600 hover:bg-stone-50 transition-colors">Cancel</button>
                    <button wire:click="recordPayment" class="flex-1 py-2.5 bg-green-600 hover:bg-green-500 text-white rounded-xl text-sm font-semibold transition-colors">Record Payment</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Refund Modal --}}
    @if($showRefundModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" wire:click="$set('showRefundModal', false)"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-semibold text-stone-800">Refund Order</h2>
                <button wire:click="$set('showRefundModal', false)" class="p-2 hover:bg-stone-100 rounded-xl text-stone-400">✕</button>
            </div>
            <div class="space-y-4">
                <p class="text-xs text-stone-500">Records a full refund against the order's most recent successful payment. For gateway-processed payments this only files an audit record — send the money back through the gateway/telco separately if it wasn't reversed automatically.</p>
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-1.5">Reason (optional)</label>
                    <input wire:model="refundReason" type="text" class="w-full px-4 py-2.5 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                </div>
                <div class="flex gap-3 pt-2">
                    <button wire:click="$set('showRefundModal', false)" class="flex-1 py-2.5 border border-stone-200 rounded-xl text-sm font-medium text-stone-600 hover:bg-stone-50 transition-colors">Cancel</button>
                    <button wire:click="refundOrder" class="flex-1 py-2.5 bg-red-600 hover:bg-red-500 text-white rounded-xl text-sm font-semibold transition-colors">Confirm Refund</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($showModal && $selectedOrder)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" wire:click="$set('showModal', false)"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b flex items-center justify-between sticky top-0 bg-white rounded-t-3xl">
                <div>
                    <h2 class="font-semibold text-stone-800">Order #{{ $selectedOrder->order_number }}</h2>
                    <div class="text-xs text-stone-400">{{ $selectedOrder->created_at->format('M d, Y h:i A') }}</div>
                </div>
                <button wire:click="$set('showModal', false)" class="p-2 hover:bg-stone-100 rounded-xl text-stone-500">✕</button>
            </div>
            <div class="p-6 space-y-5">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div class="bg-stone-50 rounded-xl p-3"><div class="text-xs text-stone-400 mb-1">Customer</div><div class="font-medium text-sm">{{ $selectedOrder->customer_name }}</div></div>
                    <div class="bg-stone-50 rounded-xl p-3"><div class="text-xs text-stone-400 mb-1">Phone</div><div class="font-medium text-sm">{{ $selectedOrder->customer_phone }}</div></div>
                    <div class="bg-stone-50 rounded-xl p-3"><div class="text-xs text-stone-400 mb-1">Type</div><div class="font-medium text-sm capitalize">{{ str_replace('_', ' ', $selectedOrder->order_type) }}</div></div>
                    <div class="bg-stone-50 rounded-xl p-3"><div class="text-xs text-stone-400 mb-1">Payment</div><div class="font-medium text-sm capitalize">{{ str_replace('_', ' ', $selectedOrder->payment_method) }}</div></div>
                    <div class="bg-stone-50 rounded-xl p-3"><div class="text-xs text-stone-400 mb-1">Pay Status</div><div class="font-medium text-sm capitalize">{{ $selectedOrder->payment_status }}</div></div>
                    @if($selectedOrder->table)<div class="bg-stone-50 rounded-xl p-3"><div class="text-xs text-stone-400 mb-1">Table</div><div class="font-medium text-sm">{{ $selectedOrder->table->number }}</div></div>@endif
                </div>

                <div>
                    <h3 class="font-semibold text-stone-700 mb-3 text-sm">Order Items</h3>
                    <div class="border border-stone-100 rounded-xl overflow-hidden">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-stone-50"><th class="text-left px-4 py-2 text-xs text-stone-500">Item</th><th class="text-center px-4 py-2 text-xs text-stone-500">Qty</th><th class="text-right px-4 py-2 text-xs text-stone-500">Subtotal</th></tr></thead>
                            <tbody class="divide-y divide-stone-50">
                                @foreach($selectedOrder->items as $item)
                                <tr><td class="px-4 py-3 font-medium text-stone-700">{{ $item->menuItem->name }}</td><td class="px-4 py-3 text-center text-stone-500">{{ $item->quantity }}</td><td class="px-4 py-3 text-right font-medium">K {{ number_format($item->subtotal, 2) }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 space-y-1 text-sm">
                        <div class="flex justify-between text-stone-500"><span>Subtotal</span><span>K {{ number_format($selectedOrder->subtotal, 2) }}</span></div>
                        <div class="flex justify-between text-stone-500"><span>Tax</span><span>K {{ number_format($selectedOrder->tax, 2) }}</span></div>
                        <div class="flex justify-between font-bold text-stone-800 border-t pt-2"><span>Total</span><span class="text-amber-600">K {{ number_format($selectedOrder->total, 2) }}</span></div>
                    </div>
                </div>

                @if($selectedOrder->delivery_address)
                <div class="bg-purple-50 border border-purple-200 rounded-xl p-4 text-sm text-purple-800">
                    <strong>📍 Delivery Address:</strong> {{ $selectedOrder->delivery_address }}
                </div>
                @endif

                @if($selectedOrder->order_type === 'delivery')
                <div class="border border-stone-100 rounded-xl overflow-hidden">
                    <div class="px-4 py-2 bg-stone-50 border-b border-stone-100 flex items-center justify-between">
                        <span class="text-xs font-semibold text-stone-500 uppercase">Delivery Tracking</span>
                        @if($selectedOrder->driver_name)
                        <span class="text-xs text-stone-500">🚴 {{ $selectedOrder->driver_name }} · {{ $selectedOrder->driver_phone }}</span>
                        @else
                        <button wire:click="openDriverModal({{ $selectedOrder->id }})"
                                class="text-xs px-3 py-1 bg-blue-600 hover:bg-blue-500 text-white rounded-lg transition-colors">
                            Assign Driver
                        </button>
                        @endif
                    </div>
                    <div class="p-4">
                        @php
                            $deliverySteps = [
                                'pending'   => ['label' => 'Pending Assignment', 'color' => 'stone'],
                                'assigned'  => ['label' => 'Driver Assigned',   'color' => 'blue'],
                                'picked_up' => ['label' => 'Picked Up',         'color' => 'amber'],
                                'delivered' => ['label' => 'Delivered',         'color' => 'green'],
                            ];
                            $currentStatus = $selectedOrder->delivery_status ?? 'pending';
                            $stepOrder = array_keys($deliverySteps);
                            $currentIndex = array_search($currentStatus, $stepOrder);
                        @endphp
                        <div class="flex items-center gap-2">
                            @foreach($deliverySteps as $key => $step)
                            @php $idx = array_search($key, $stepOrder); $done = $idx <= $currentIndex; @endphp
                            <div class="flex items-center gap-1 flex-1 {{ !$loop->last ? '' : '' }}">
                                <div class="flex flex-col items-center flex-1">
                                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                                        {{ $done ? 'bg-green-500 text-white' : 'bg-stone-100 text-stone-400' }}">
                                        {{ $done ? '✓' : ($idx + 1) }}
                                    </div>
                                    <span class="text-xs text-stone-500 mt-1 text-center leading-tight">{{ $step['label'] }}</span>
                                </div>
                                @if(!$loop->last)
                                <div class="h-0.5 flex-1 mb-4 {{ $done && $idx < $currentIndex ? 'bg-green-400' : 'bg-stone-100' }}"></div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @if($selectedOrder->driver_name && $currentStatus !== 'delivered')
                        <div class="mt-4 flex gap-2 flex-wrap">
                            @if(in_array($currentStatus, ['assigned']))
                            <button wire:click="updateDeliveryStatus({{ $selectedOrder->id }}, 'picked_up')"
                                    class="px-3 py-1.5 text-xs bg-amber-600 hover:bg-amber-500 text-white rounded-lg transition-colors">
                                Mark Picked Up
                            </button>
                            @endif
                            @if(in_array($currentStatus, ['picked_up']))
                            <button wire:click="updateDeliveryStatus({{ $selectedOrder->id }}, 'delivered')"
                                    class="px-3 py-1.5 text-xs bg-green-600 hover:bg-green-500 text-white rounded-lg transition-colors">
                                Mark Delivered
                            </button>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                @if($selectedOrder->notes)
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-800">
                    <strong>Notes:</strong> {{ $selectedOrder->notes }}
                </div>
                @endif

                <div class="flex gap-3 flex-wrap">
                    @foreach(['confirmed', 'preparing', 'ready', 'completed', 'cancelled'] as $s)
                    @if($s !== $selectedOrder->status)
                    <button wire:click="updateStatus({{ $selectedOrder->id }}, '{{ $s }}')"
                            class="px-4 py-2 text-sm font-medium rounded-xl border transition-colors
                            {{ $s === 'cancelled' ? 'border-red-300 text-red-600 hover:bg-red-50' : 'border-amber-300 text-amber-700 hover:bg-amber-50' }}">
                        Mark {{ ucfirst($s) }}
                    </button>
                    @endif
                    @endforeach
                    @if($selectedOrder->payment_status !== 'paid')
                    <button wire:click="openRecordPaymentModal({{ $selectedOrder->id }})" class="px-4 py-2 text-sm font-medium rounded-xl bg-green-600 hover:bg-green-500 text-white transition-colors">Record Payment</button>
                    @else
                    <button wire:click="openRefundModal({{ $selectedOrder->id }})" class="px-4 py-2 text-sm font-medium rounded-xl bg-red-600 hover:bg-red-500 text-white transition-colors">Refund</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
