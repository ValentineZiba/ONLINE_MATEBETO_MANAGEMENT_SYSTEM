<div class="space-y-5">
    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">⚠️ {{ session('error') }}</div>
    @endif

    <div class="flex justify-end">
        <button wire:click="openCreate" class="px-5 py-2 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-sm font-semibold transition-colors flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Rider
        </button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($riders as $rider)
        <div class="bg-white rounded-2xl shadow-sm border border-stone-100 overflow-hidden">
            <div class="p-5">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-14 h-14 rounded-xl overflow-hidden bg-stone-100 flex items-center justify-center flex-shrink-0">
                        @if($rider->rider_photo_path)
                        <img src="{{ route('admin.delivery-riders.document', [$rider->id, 'photo']) }}" class="w-full h-full object-cover" alt="{{ $rider->name }}">
                        @else
                        <span class="text-2xl">{{ $rider->vehicle_icon }}</span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-stone-800 truncate">{{ $rider->name }}</div>
                        <div class="text-xs text-stone-500">{{ $rider->phone }}</div>
                        <div class="text-xs text-stone-500 capitalize">{{ $rider->vehicle_icon }} {{ $rider->vehicle_type }}</div>
                    </div>
                    <button wire:click="toggle({{ $rider->id }})" class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors flex-shrink-0 {{ $rider->is_active ? 'bg-green-500' : 'bg-stone-200' }}">
                        <span class="inline-block h-3 w-3 transform rounded-full bg-white shadow transition-transform {{ $rider->is_active ? 'translate-x-5' : 'translate-x-1' }}"></span>
                    </button>
                </div>
                <div class="text-xs text-stone-500 space-y-1">
                    @if($rider->requiresPlateNumber())
                    <div><span class="text-stone-400">Plate:</span> <strong class="text-stone-700 font-mono">{{ $rider->plate_number }}</strong></div>
                    @endif
                    @if($rider->requiresNrc())
                    <div class="flex gap-2 items-center">
                        <span class="text-stone-400">NRC:</span>
                        @if($rider->nrc_front_path)
                        <a href="{{ route('admin.delivery-riders.document', [$rider->id, 'nrc_front']) }}" target="_blank" class="text-amber-600 hover:underline">Front</a>
                        @endif
                        @if($rider->nrc_back_path)
                        <a href="{{ route('admin.delivery-riders.document', [$rider->id, 'nrc_back']) }}" target="_blank" class="text-amber-600 hover:underline">Back</a>
                        @endif
                    </div>
                    @endif
                    <div><span class="text-stone-400">Deliveries:</span> {{ $rider->orders_count }}</div>
                </div>
            </div>
            <div class="border-t border-stone-50 px-5 py-3 flex gap-3">
                <button wire:click="openEdit({{ $rider->id }})" class="text-sm text-amber-600 hover:text-amber-700 font-medium">Edit</button>
                <button wire:click="delete({{ $rider->id }})" wire:confirm="Delete rider {{ $rider->name }}?" class="text-sm text-red-500 hover:text-red-600 font-medium">Delete</button>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-16 text-stone-400">No delivery riders registered yet.</div>
        @endforelse
    </div>

    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" wire:click="$set('showModal', false)"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b flex items-center justify-between sticky top-0 bg-white rounded-t-3xl">
                <h2 class="font-semibold text-stone-800">{{ $editing ? 'Edit Rider' : 'Register Rider' }}</h2>
                <button wire:click="$set('showModal', false)" class="p-2 hover:bg-stone-100 rounded-xl text-stone-500">✕</button>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-2">Name *</label>
                        <input wire:model="name" type="text" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                        @error('name') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-2">Phone *</label>
                        <input wire:model="phone" type="tel" placeholder="+260..." class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                        @error('phone') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-2">Vehicle Type *</label>
                    <div class="flex gap-3">
                        @foreach(['car' => '🚗 Car', 'motorbike' => '🏍️ Motorbike', 'bicycle' => '🚲 Bicycle'] as $val => $lbl)
                        <button type="button" wire:click="$set('vehicleType', '{{ $val }}')"
                                class="flex-1 py-2.5 rounded-xl text-sm font-medium border transition-colors
                                {{ $vehicleType === $val ? 'border-amber-500 bg-amber-50 text-amber-700' : 'border-stone-200 text-stone-600 hover:bg-stone-50' }}">
                            {{ $lbl }}
                        </button>
                        @endforeach
                    </div>
                </div>

                @if($vehicleType !== 'bicycle')
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-2">Plate Number *</label>
                    <input wire:model="plateNumber" type="text" placeholder="ABC 1234" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 uppercase">
                    @error('plateNumber') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                </div>
                @endif

                <div>
                    <x-camera-capture model="riderPhotoData" label="Live Picture of Rider *" />
                    @if($hasExistingPhoto && !$riderPhotoData)
                    <p class="text-xs text-stone-400 mt-1">Existing photo on file — capture a new one to replace it.</p>
                    @endif
                    @error('riderPhotoData') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                </div>

                @if($vehicleType === 'bicycle')
                <div>
                    <x-camera-capture model="nrcFrontData" label="NRC — Front Side *" />
                    @if($hasExistingNrcFront && !$nrcFrontData)
                    <p class="text-xs text-stone-400 mt-1">Existing NRC front on file — capture a new one to replace it.</p>
                    @endif
                    @error('nrcFrontData') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                </div>
                <div>
                    <x-camera-capture model="nrcBackData" label="NRC — Back Side *" />
                    @if($hasExistingNrcBack && !$nrcBackData)
                    <p class="text-xs text-stone-400 mt-1">Existing NRC back on file — capture a new one to replace it.</p>
                    @endif
                    @error('nrcBackData') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                </div>
                @endif

                <label class="flex items-center gap-2 cursor-pointer">
                    <input wire:model="isActive" type="checkbox" class="h-4 w-4 accent-amber-500">
                    <span class="text-sm text-stone-700">Active (assignable to deliveries)</span>
                </label>

                <div class="flex gap-3 pt-2">
                    <button wire:click="save" class="flex-1 py-3 bg-amber-600 hover:bg-amber-500 text-white font-semibold rounded-xl">{{ $editing ? 'Update' : 'Register' }}</button>
                    <button wire:click="$set('showModal', false)" class="px-6 py-3 border border-stone-200 hover:bg-stone-50 text-stone-600 rounded-xl">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
