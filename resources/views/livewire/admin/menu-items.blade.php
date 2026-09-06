<div class="space-y-5">
    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">✅ {{ session('success') }}</div>
    @endif

    <div class="flex flex-wrap gap-3 items-center justify-between">
        <div class="flex gap-3 flex-wrap">
            <div class="relative"><svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search items..." class="pl-10 pr-4 py-2 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 w-48">
            </div>
            <select wire:model.live="categoryFilter" class="px-3 py-2 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->icon }} {{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <button wire:click="openCreate" class="px-5 py-2 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-sm font-semibold transition-colors flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Item
        </button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-stone-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead><tr class="bg-stone-50 text-left">
                    <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase">Item</th>
                    <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase">Category</th>
                    <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase">Price</th>
                    <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase">Prep</th>
                    <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase">Badges</th>
                    <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase">Available</th>
                    <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase">Featured</th>
                    <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase">Actions</th>
                </tr></thead>
                <tbody class="divide-y divide-stone-50">
                    @forelse($items as $item)
                    <tr class="hover:bg-stone-50 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if($item->image)
                                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}"
                                         style="width:44px;height:44px;object-fit:cover;border-radius:10px;flex-shrink:0">
                                @else
                                    <div style="width:44px;height:44px;border-radius:10px;flex-shrink:0;background:#f1f5f9;display:flex;align-items:center;justify-content:center">
                                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#94a3b8"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                                <div>
                                    <div class="font-medium text-stone-800 text-sm">{{ $item->name }}</div>
                                    <div class="text-stone-400 text-xs truncate max-w-xs">{{ Str::limit($item->description, 45) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $item->category->icon }} {{ $item->category->name }}</td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-stone-800 text-sm">K {{ number_format($item->effective_price, 0) }}</div>
                            @if($item->discount_price)<div class="text-xs text-stone-400 line-through">K {{ number_format($item->price, 0) }}</div>@endif
                        </td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $item->preparation_time }}min</td>
                        <td class="px-6 py-4">
                            <div class="flex gap-1 flex-wrap">
                                @if($item->is_vegetarian)<span class="text-xs bg-green-100 text-green-700 px-1.5 py-0.5 rounded">🌿</span>@endif
                                @if($item->is_vegan)<span class="text-xs bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded">🌱</span>@endif
                                @if($item->is_spicy)<span class="text-xs bg-red-100 text-red-700 px-1.5 py-0.5 rounded">🌶</span>@endif
                                @if($item->is_gluten_free)<span class="text-xs bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded">GF</span>@endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1.5 items-start">
                                <button wire:click="toggleAvailable({{ $item->id }})" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $item->is_available ? 'bg-green-500' : 'bg-stone-200' }}">
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform {{ $item->is_available ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                </button>
                                @if($item->stock_quantity !== null)
                                <span class="text-xs px-1.5 py-0.5 rounded font-medium {{ $item->stock_quantity <= 5 ? 'bg-red-100 text-red-600' : 'bg-stone-100 text-stone-500' }}">
                                    Stock: {{ $item->stock_quantity }}
                                </span>
                                @endif
                                @if(($item->station ?? 'kitchen') !== 'kitchen')
                                <span class="text-xs px-1.5 py-0.5 rounded font-medium bg-amber-100 text-amber-700">
                                    {{ $item->station === 'bar' ? '🍸 Bar' : '🍸 Both' }}
                                </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <button wire:click="toggleFeatured({{ $item->id }})" class="text-lg transition-opacity {{ $item->is_featured ? 'opacity-100' : 'opacity-30' }}">⭐</button>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <button wire:click="openEdit({{ $item->id }})" class="text-sm text-amber-600 hover:text-amber-700 font-medium">Edit</button>
                                <button wire:click="delete({{ $item->id }})" wire:confirm="Delete this item?" class="text-sm text-red-500 hover:text-red-600 font-medium">Del</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-16 text-stone-400">No menu items found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-stone-50">{{ $items->links() }}</div>
    </div>

    {{-- Modal --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" wire:click="$set('showModal', false)"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-2xl">
            {{-- Header --}}
            <div class="px-6 py-4 border-b flex items-center justify-between">
                <h2 class="font-semibold text-stone-800">{{ $editing ? 'Edit Menu Item' : 'Add Menu Item' }}</h2>
                <button wire:click="$set('showModal', false)" class="p-2 hover:bg-stone-100 rounded-xl text-stone-500">✕</button>
            </div>

            {{-- Two-column body --}}
            <div class="p-5 flex gap-5">

                {{-- Left: Photo --}}
                <div class="flex-shrink-0 space-y-2" style="width:144px;min-width:144px">
                    <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wide">Photo</label>
                    @if($existingImage && !$imageFile)
                        <img src="{{ Storage::url($existingImage) }}"
                             class="object-cover rounded-2xl border border-stone-200"
                             style="width:144px;height:144px;object-fit:cover">
                    @elseif($imageFile)
                        <img src="{{ $imageFile->temporaryUrl() }}"
                             class="object-cover rounded-2xl border border-amber-200"
                             style="width:144px;height:144px;object-fit:cover">
                    @else
                        <div class="rounded-2xl bg-stone-100 border-2 border-dashed border-stone-300 flex flex-col items-center justify-center text-stone-400 text-xs text-center"
                             style="width:144px;height:144px">
                            <svg class="w-8 h-8 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            No photo
                        </div>
                    @endif
                    <input wire:model="imageFile" type="file" accept="image/*" id="imageFileInput"
                           class="hidden">
                    <label for="imageFileInput" class="cursor-pointer block text-center py-1.5 px-3 bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-semibold rounded-lg border border-amber-200 transition-colors">
                        {{ $imageFile || $existingImage ? 'Change Photo' : 'Add Photo' }}
                    </label>
                    <div wire:loading wire:target="imageFile" class="text-xs text-amber-600 text-center">Uploading…</div>
                    @error('imageFile') <p class="text-xs text-red-500 text-center">{{ $message }}</p> @enderror
                </div>

                {{-- Right: Fields --}}
                <div class="flex-1 space-y-3">
                    {{-- Name --}}
                    <div>
                        <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wide mb-1">Item Name *</label>
                        <input wire:model="name" type="text" placeholder="e.g. Grilled Chicken" class="w-full px-3 py-2 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        @error('name') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- Category + Price --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wide mb-1">Category *</label>
                            <select wire:model="categoryId" class="w-full px-3 py-2 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white">
                                <option value="">Select…</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->icon }} {{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('categoryId') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wide mb-1">Price (K) *</label>
                            <input wire:model="price" type="number" step="0.01" placeholder="0.00" class="w-full px-3 py-2 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                            @error('price') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Discount + Prep + Calories --}}
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wide mb-1">Discount (K)</label>
                            <input wire:model="discountPrice" type="number" step="0.01" placeholder="optional" class="w-full px-3 py-2 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wide mb-1">Prep (mins)</label>
                            <input wire:model="preparationTime" type="number" class="w-full px-3 py-2 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wide mb-1">Calories</label>
                            <input wire:model="calories" type="number" placeholder="optional" class="w-full px-3 py-2 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wide mb-1">Stock Qty</label>
                            <input wire:model="stockQuantity" type="number" min="0" placeholder="∞ unlimited" class="w-full px-3 py-2 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wide mb-1">Station</label>
                            <select wire:model="station" class="w-full px-3 py-2 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white">
                                <option value="kitchen">🍳 Kitchen</option>
                                <option value="bar">🍸 Bar</option>
                                <option value="both">🍳🍸 Both</option>
                            </select>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wide mb-1">Description</label>
                        <textarea wire:model="description" rows="2" placeholder="Brief description of the dish…" class="w-full px-3 py-2 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 resize-none"></textarea>
                    </div>

                    {{-- Badges --}}
                    <div class="grid grid-cols-3 gap-2">
                        @foreach([
                            ['model' => 'isAvailable',  'label' => '✅ Available'],
                            ['model' => 'isFeatured',   'label' => '⭐ Featured'],
                            ['model' => 'isVegetarian', 'label' => '🌿 Vegetarian'],
                            ['model' => 'isVegan',      'label' => '🌱 Vegan'],
                            ['model' => 'isGlutenFree', 'label' => '🔵 Gluten Free'],
                            ['model' => 'isSpicy',      'label' => '🌶 Spicy'],
                        ] as $toggle)
                        <label class="flex items-center gap-1.5 cursor-pointer p-2 border border-stone-200 rounded-lg hover:border-amber-300 transition-colors text-xs">
                            <input wire:model="{{ $toggle['model'] }}" type="checkbox" class="h-3.5 w-3.5 accent-amber-500 flex-shrink-0">
                            <span class="text-stone-700">{{ $toggle['label'] }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-5 pb-5 space-y-3">
                @if($errors->any())
                <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-red-600 text-sm">
                    <span class="font-medium">Please fill in required fields: </span>{{ implode(', ', $errors->all()) }}
                </div>
                @endif
                <div class="flex gap-3">
                    <button wire:click="save" wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-not-allowed"
                            class="flex-1 py-2.5 bg-amber-600 hover:bg-amber-500 text-white font-semibold rounded-xl transition-colors text-sm">
                        <span wire:loading wire:target="save">Saving…</span>
                        <span wire:loading.remove wire:target="save">{{ $editing ? 'Update Item' : 'Create Item' }}</span>
                    </button>
                    <button wire:click="$set('showModal', false)" class="px-5 py-2.5 border border-stone-200 hover:bg-stone-50 text-stone-600 font-medium rounded-xl transition-colors text-sm">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
