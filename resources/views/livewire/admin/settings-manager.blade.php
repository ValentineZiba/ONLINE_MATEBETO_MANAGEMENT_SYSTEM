<div class="space-y-6">
    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">✅ {{ session('success') }}</div>
    @endif

    <form wire:submit="save" class="space-y-6">
        {{-- General --}}
        <div class="bg-white rounded-2xl shadow-sm border border-stone-100 overflow-hidden">
            <div class="px-6 py-4 border-b bg-stone-50">
                <h3 class="font-semibold text-stone-800">🏠 General</h3>
                <p class="text-xs text-stone-400 mt-0.5">Basic restaurant information</p>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-stone-700 mb-2">Restaurant Name *</label>
                    <input wire:model="restaurantName" type="text" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                    @error('restaurantName') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-stone-700 mb-2">Tagline</label>
                    <input wire:model="restaurantTagline" type="text" placeholder="Fine dining & catering" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-2">Currency Code</label>
                    <input wire:model="currency" type="text" placeholder="K" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-2">Currency Symbol</label>
                    <input wire:model="currencySymbol" type="text" placeholder="K" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                </div>
            </div>
        </div>

        {{-- Contact --}}
        <div class="bg-white rounded-2xl shadow-sm border border-stone-100 overflow-hidden">
            <div class="px-6 py-4 border-b bg-stone-50">
                <h3 class="font-semibold text-stone-800">📞 Contact & Location</h3>
                <p class="text-xs text-stone-400 mt-0.5">Phone, email and location details</p>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-2">Email</label>
                    <input wire:model="restaurantEmail" type="email" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                    @error('restaurantEmail') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-2">Phone</label>
                    <input wire:model="restaurantPhone" type="text" placeholder="+260 977 000000" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-stone-700 mb-2">Address</label>
                    <textarea wire:model="restaurantAddress" rows="2" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-2">Latitude</label>
                    <input wire:model="restaurantLatitude" type="text" placeholder="-15.3875" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                    @error('restaurantLatitude') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-2">Longitude</label>
                    <input wire:model="restaurantLongitude" type="text" placeholder="28.3228" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                    @error('restaurantLongitude') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                </div>
                <p class="sm:col-span-2 text-xs text-stone-400">Used to calculate distance-based delivery fees. Find these on Google Maps: right-click your restaurant's exact location → the coordinates appear at the top of the menu, ready to copy.</p>
            </div>
        </div>

        {{-- Hours & Billing --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl shadow-sm border border-stone-100 overflow-hidden">
                <div class="px-6 py-4 border-b bg-stone-50">
                    <h3 class="font-semibold text-stone-800">🕐 Hours & Billing</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-2">Opening Hours</label>
                        <input wire:model="openingHours" type="text" placeholder="Mon-Sun 8AM-10PM" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-2">Tax Rate (%)</label>
                        <input wire:model="taxRate" type="number" step="0.01" min="0" max="100" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                        @error('taxRate') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-semibold text-stone-700 mb-2">Delivery Base Fee (K)</label>
                            <input wire:model="deliveryBaseFee" type="number" step="0.01" min="0" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                            @error('deliveryBaseFee') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-stone-700 mb-2">Rate per KM (K)</label>
                            <input wire:model="deliveryRatePerKm" type="number" step="0.01" min="0" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                            @error('deliveryRatePerKm') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <p class="text-xs text-stone-400 -mt-2">Delivery fee = base fee + (rate × distance in km) from the restaurant to the delivery address.</p>
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-2">Fallback Delivery Fee (K)</label>
                        <input wire:model="deliveryFee" type="number" step="0.01" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                        <p class="text-xs text-stone-400 mt-1">Used only when the delivery address can't be located automatically.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-2">Min Order Amount (K)</label>
                        <input wire:model="minOrderAmount" type="number" step="0.01" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-stone-100 overflow-hidden">
                <div class="px-6 py-4 border-b bg-stone-50">
                    <h3 class="font-semibold text-stone-800">📱 Social Media</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-2">Facebook URL</label>
                        <input wire:model="facebookUrl" type="url" placeholder="https://facebook.com/..." class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-2">Instagram URL</label>
                        <input wire:model="instagramUrl" type="url" placeholder="https://instagram.com/..." class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-2">Twitter / X URL</label>
                        <input wire:model="twitterUrl" type="url" placeholder="https://x.com/..." class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-8 py-3 bg-amber-600 hover:bg-amber-500 text-white font-semibold rounded-xl transition-colors shadow-lg shadow-amber-200">
                Save All Settings
            </button>
        </div>
    </form>
</div>
