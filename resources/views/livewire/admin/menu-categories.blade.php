<div class="space-y-5">
    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">✅ {{ session('success') }}</div>
    @endif

    <div class="flex justify-end">
        <button wire:click="openCreate" class="px-5 py-2 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-sm font-semibold transition-colors flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Category
        </button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($categories as $cat)
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-stone-100 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div class="text-4xl">{{ $cat->icon ?? '🍽️' }}</div>
                <div class="flex items-center gap-2">
                    <button wire:click="toggle({{ $cat->id }})" class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors {{ $cat->is_active ? 'bg-green-500' : 'bg-stone-200' }}">
                        <span class="inline-block h-3 w-3 transform rounded-full bg-white shadow transition-transform {{ $cat->is_active ? 'translate-x-5' : 'translate-x-1' }}"></span>
                    </button>
                </div>
            </div>
            <h3 class="font-semibold text-stone-800">{{ $cat->name }}</h3>
            <p class="text-xs text-stone-400 mt-1 mb-3 line-clamp-2">{{ $cat->description ?: 'No description' }}</p>
            <div class="flex items-center justify-between">
                <span class="text-xs text-stone-500">{{ $cat->menu_items_count }} items</span>
                <div class="flex gap-2">
                    <button wire:click="openEdit({{ $cat->id }})" class="text-sm text-amber-600 hover:text-amber-700 font-medium">Edit</button>
                    <button wire:click="delete({{ $cat->id }})" wire:confirm="Delete this category?" class="text-sm text-red-500 hover:text-red-600 font-medium">Delete</button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-16 text-stone-400">No categories yet</div>
        @endforelse
    </div>

    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" wire:click="$set('showModal', false)"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md">
            <div class="p-6 border-b flex items-center justify-between">
                <h2 class="font-semibold text-stone-800">{{ $editing ? 'Edit Category' : 'Add Category' }}</h2>
                <button wire:click="$set('showModal', false)" class="p-2 hover:bg-stone-100 rounded-xl text-stone-500">✕</button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-2">Name *</label>
                    <input wire:model="name" type="text" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                    @error('name') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-2">Icon (emoji)</label>
                        <input wire:model="icon" type="text" placeholder="🍽️" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-center text-2xl">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-2">Sort Order</label>
                        <input wire:model="sortOrder" type="number" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-2">Description</label>
                    <textarea wire:model="description" rows="2" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 resize-none"></textarea>
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input wire:model="isActive" type="checkbox" class="h-4 w-4 accent-amber-500">
                    <span class="text-sm text-stone-700">Active (visible on menu)</span>
                </label>
                <div class="flex gap-3">
                    <button wire:click="save" class="flex-1 py-3 bg-amber-600 hover:bg-amber-500 text-white font-semibold rounded-xl">{{ $editing ? 'Update' : 'Create' }}</button>
                    <button wire:click="$set('showModal', false)" class="px-6 py-3 border border-stone-200 hover:bg-stone-50 text-stone-600 rounded-xl">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
