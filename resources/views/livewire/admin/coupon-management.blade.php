<div class="space-y-5">
    <div class="flex justify-end">
        <button wire:click="openCreate" class="px-5 py-2 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-sm font-semibold transition-colors flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Coupon
        </button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($coupons as $coupon)
        <div class="bg-white rounded-2xl shadow-sm border border-stone-100 overflow-hidden">
            <div class="p-5">
                <div class="flex items-start justify-between mb-3">
                    <div class="bg-amber-50 border border-dashed border-amber-300 rounded-xl px-4 py-2">
                        <span class="font-mono font-bold text-amber-700 text-lg tracking-widest">{{ $coupon->code }}</span>
                    </div>
                    <button wire:click="toggle({{ $coupon->id }})" class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors {{ $coupon->is_active ? 'bg-green-500' : 'bg-stone-200' }}">
                        <span class="inline-block h-3 w-3 transform rounded-full bg-white shadow transition-transform {{ $coupon->is_active ? 'translate-x-5' : 'translate-x-1' }}"></span>
                    </button>
                </div>
                <p class="text-sm text-stone-600 mb-3">{{ $coupon->description }}</p>
                <div class="grid grid-cols-2 gap-2 text-xs text-stone-500">
                    <div><span class="text-stone-400">Discount:</span> <strong class="text-amber-600">{{ $coupon->type === 'percentage' ? $coupon->value . '%' : 'K ' . number_format($coupon->value, 0) }}</strong></div>
                    <div><span class="text-stone-400">Min order:</span> K {{ number_format($coupon->min_order_amount, 0) }}</div>
                    <div><span class="text-stone-400">Used:</span> {{ $coupon->uses_count }}{{ $coupon->max_uses ? '/' . $coupon->max_uses : '' }}</div>
                    <div><span class="text-stone-400">Status:</span> {{ $coupon->is_active ? 'Active' : 'Inactive' }}</div>
                    @if($coupon->expires_at)
                    <div class="col-span-2"><span class="text-stone-400">Expires:</span> {{ $coupon->expires_at->format('M d, Y') }}</div>
                    @endif
                </div>
            </div>
            <div class="border-t border-stone-50 px-5 py-3 flex gap-3">
                <button wire:click="openEdit({{ $coupon->id }})" class="text-sm text-amber-600 hover:text-amber-700 font-medium">Edit</button>
                <button wire:click="delete({{ $coupon->id }})" wire:confirm="Delete coupon {{ $coupon->code }}?" class="text-sm text-red-500 hover:text-red-600 font-medium">Delete</button>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-16 text-stone-400">No coupons yet. Create your first discount!</div>
        @endforelse
    </div>

    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" wire:click="$set('showModal', false)"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b flex items-center justify-between sticky top-0 bg-white rounded-t-3xl">
                <h2 class="font-semibold text-stone-800">{{ $editing ? 'Edit Coupon' : 'Create Coupon' }}</h2>
                <button wire:click="$set('showModal', false)" class="p-2 hover:bg-stone-100 rounded-xl text-stone-500">✕</button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-2">Coupon Code *</label>
                    <input wire:model="code" type="text" placeholder="SAVE20" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 font-mono uppercase">
                    @error('code') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-2">Description</label>
                    <input wire:model="description" type="text" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-2">Discount Type *</label>
                        <select wire:model.live="type" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white">
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed">Fixed Amount (K)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-2">Value *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-stone-400 text-sm">{{ $type === 'percentage' ? '%' : 'K' }}</span>
                            <input wire:model="value" type="number" step="0.01" class="w-full pl-10 pr-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        @error('value') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-2">Min Order (K)</label>
                        <input wire:model="minOrderAmount" type="number" step="0.01" placeholder="0" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    @if($type === 'percentage')
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-2">Max Discount (K)</label>
                        <input wire:model="maxDiscount" type="number" step="0.01" placeholder="No cap" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    @endif
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-2">Total Uses Limit</label>
                        <input wire:model="maxUses" type="number" placeholder="Unlimited" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-2">Expiry Date</label>
                        <input wire:model="expiresAt" type="date" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input wire:model="isActive" type="checkbox" class="h-4 w-4 accent-amber-500">
                    <span class="text-sm text-stone-700">Active (usable by customers)</span>
                </label>
                <div class="flex gap-3 pt-2">
                    <button wire:click="save" class="flex-1 py-3 bg-amber-600 hover:bg-amber-500 text-white font-semibold rounded-xl">{{ $editing ? 'Update' : 'Create' }}</button>
                    <button wire:click="$set('showModal', false)" class="px-6 py-3 border border-stone-200 hover:bg-stone-50 text-stone-600 rounded-xl">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
