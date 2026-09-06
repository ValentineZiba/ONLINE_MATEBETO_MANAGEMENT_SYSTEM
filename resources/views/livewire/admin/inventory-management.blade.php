<div class="space-y-6">
    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">✅ {{ session('success') }}</div>
    @endif

    {{-- Header --}}
    <div class="flex flex-wrap gap-3 items-center justify-between">
        <div class="flex gap-3 flex-wrap">
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search inventory..."
                       class="pl-10 pr-4 py-2 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 w-52">
            </div>
            @if($categories->count())
            <select wire:model.live="categoryFilter" class="px-3 py-2 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                <option value="{{ $cat }}">{{ $cat }}</option>
                @endforeach
            </select>
            @endif
            @if($lowCount)
            <span class="px-3 py-2 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm font-medium">
                ⚠️ {{ $lowCount }} low stock
            </span>
            @endif
        </div>
        <button wire:click="openCreate" class="px-5 py-2 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-sm font-semibold transition-colors flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Item
        </button>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-stone-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-stone-50 text-left">
                        <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase">Item</th>
                        <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase">Category</th>
                        <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase">Qty</th>
                        <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase">Threshold</th>
                        <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase">Cost/Unit</th>
                        <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase">Supplier</th>
                        <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase">Last Restock</th>
                        <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-50">
                    @forelse($items as $item)
                    <tr class="hover:bg-stone-50 transition-colors {{ $item->is_low_stock ? 'bg-red-50/30' : '' }}">
                        <td class="px-6 py-4">
                            <div class="font-medium text-stone-800 text-sm">{{ $item->name }}</div>
                            <div class="text-xs text-stone-400">{{ $item->unit }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-stone-500">{{ $item->category ?? '—' }}</td>
                        <td class="px-6 py-4">
                            <span class="font-semibold text-sm {{ $item->is_low_stock ? 'text-red-600' : 'text-stone-800' }}">
                                {{ number_format($item->quantity, 1) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-stone-500">{{ $item->low_stock_threshold }} {{ $item->unit }}</td>
                        <td class="px-6 py-4 text-sm text-stone-600">K {{ number_format($item->cost_per_unit, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-stone-500">{{ $item->supplier ?? '—' }}</td>
                        <td class="px-6 py-4">
                            @if($item->is_low_stock)
                            <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-700 font-medium">Low Stock</span>
                            @elseif(!$item->is_active)
                            <span class="text-xs px-2 py-1 rounded-full bg-stone-100 text-stone-500">Inactive</span>
                            @else
                            <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-medium">OK</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xs text-stone-400">
                            {{ $item->last_restocked_at?->diffForHumans() ?? 'Never' }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <button wire:click="openRestock({{ $item->id }})"
                                        class="text-xs px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition-colors font-medium">
                                    Restock
                                </button>
                                <button wire:click="openEdit({{ $item->id }})"
                                        class="text-xs px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg transition-colors font-medium">
                                    Edit
                                </button>
                                <button wire:click="delete({{ $item->id }})" wire:confirm="Delete this item?"
                                        class="text-xs px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition-colors font-medium">
                                    Del
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center py-16 text-stone-400">No inventory items found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-stone-50">{{ $items->links() }}</div>
    </div>

    {{-- Add/Edit Modal --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" wire:click="$set('showModal', false)"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b flex items-center justify-between sticky top-0 bg-white rounded-t-3xl">
                <h2 class="font-semibold text-stone-800">{{ $editId ? 'Edit Item' : 'Add Inventory Item' }}</h2>
                <button wire:click="$set('showModal', false)" class="p-2 hover:bg-stone-100 rounded-xl text-stone-400">✕</button>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-stone-700 mb-1.5">Item Name *</label>
                        <input wire:model="name" type="text" class="w-full px-4 py-2.5 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                        @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-1.5">Unit *</label>
                        <select wire:model="unit" class="w-full px-4 py-2.5 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm bg-white">
                            @foreach(['pcs','kg','g','liters','ml','boxes','bags','dozen'] as $u)
                            <option value="{{ $u }}">{{ $u }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-1.5">Category</label>
                        <input wire:model="category" type="text" placeholder="e.g. Produce, Dairy" class="w-full px-4 py-2.5 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-1.5">Current Quantity *</label>
                        <input wire:model="quantity" type="number" step="0.01" min="0" class="w-full px-4 py-2.5 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                        @error('quantity') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-1.5">Low Stock Alert *</label>
                        <input wire:model="lowStockThreshold" type="number" step="0.01" min="0" class="w-full px-4 py-2.5 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-1.5">Cost per Unit (K)</label>
                        <input wire:model="costPerUnit" type="number" step="0.01" min="0" class="w-full px-4 py-2.5 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-1.5">Supplier</label>
                        <input wire:model="supplier" type="text" class="w-full px-4 py-2.5 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                    </div>
                    <div class="col-span-2 flex items-center gap-3">
                        <input wire:model="isActive" type="checkbox" id="inv-active" class="w-4 h-4 accent-amber-600">
                        <label for="inv-active" class="text-sm text-stone-700">Item is active</label>
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button wire:click="$set('showModal', false)" class="flex-1 py-2.5 border border-stone-200 rounded-xl text-sm font-medium text-stone-600 hover:bg-stone-50 transition-colors">Cancel</button>
                    <button wire:click="save" class="flex-1 py-2.5 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-sm font-semibold transition-colors">Save</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Restock Modal --}}
    @if($showRestockModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" wire:click="$set('showRestockModal', false)"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-sm p-6">
            <h2 class="font-semibold text-stone-800 mb-4">Restock Item</h2>
            <div>
                <label class="block text-sm font-semibold text-stone-700 mb-1.5">Amount to Add *</label>
                <input wire:model="restockAmount" type="number" step="0.01" min="0.001" autofocus
                       class="w-full px-4 py-2.5 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                @error('restockAmount') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <div class="flex gap-3 mt-4">
                <button wire:click="$set('showRestockModal', false)" class="flex-1 py-2.5 border border-stone-200 rounded-xl text-sm font-medium text-stone-600 hover:bg-stone-50 transition-colors">Cancel</button>
                <button wire:click="restock" class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-sm font-semibold transition-colors">Add Stock</button>
            </div>
        </div>
    </div>
    @endif
</div>
