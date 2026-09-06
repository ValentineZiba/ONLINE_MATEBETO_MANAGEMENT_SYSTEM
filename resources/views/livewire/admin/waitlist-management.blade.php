<div class="space-y-6">
    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">✅ {{ session('success') }}</div>
    @endif

    {{-- Stats bar --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 text-center">
            <div class="text-3xl font-bold text-amber-600">{{ $waiting }}</div>
            <div class="text-xs text-stone-400 mt-1">Waiting</div>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 text-center">
            <div class="text-3xl font-bold text-blue-600">{{ $notified }}</div>
            <div class="text-xs text-stone-400 mt-1">Notified</div>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 text-center">
            <div class="text-3xl font-bold text-green-600">{{ $seated->count() }}</div>
            <div class="text-xs text-stone-400 mt-1">Seated Today</div>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 col-span-2 sm:col-span-1 flex items-center justify-center">
            <button wire:click="$set('showModal', true)"
                    class="w-full py-3 bg-amber-600 hover:bg-amber-500 text-white rounded-xl font-semibold text-sm transition-colors flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add to Waitlist
            </button>
        </div>
    </div>

    {{-- Active Waitlist --}}
    <div class="bg-white rounded-2xl shadow-sm border border-stone-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-stone-100 flex items-center justify-between">
            <h2 class="font-semibold text-stone-800">Active Waitlist</h2>
            <span class="text-xs text-stone-400">Auto-refreshes every 20s</span>
        </div>
        @forelse($active as $entry)
        <div class="px-6 py-4 border-b border-stone-50 last:border-0 flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="font-semibold text-stone-800 text-sm">{{ $entry->guest_name }}</span>
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium
                        {{ $entry->status === 'notified' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ ucfirst($entry->status) }}
                    </span>
                </div>
                <div class="text-xs text-stone-400 mt-1">
                    👥 Party of {{ $entry->party_size }} &nbsp;·&nbsp;
                    📞 {{ $entry->phone }}
                    @if($entry->email) &nbsp;·&nbsp; ✉️ {{ $entry->email }} @endif
                </div>
                @if($entry->notes)
                <div class="text-xs text-stone-500 mt-1 italic">{{ $entry->notes }}</div>
                @endif
            </div>
            <div class="text-right">
                <div class="text-sm font-semibold text-amber-600">~{{ $entry->wait_minutes }} min</div>
                <div class="text-xs text-stone-400">{{ $entry->created_at->format('h:i A') }}</div>
            </div>
            <div class="flex gap-2 flex-wrap">
                @if($entry->status === 'waiting')
                <button wire:click="notify({{ $entry->id }})"
                        class="px-3 py-1.5 text-xs font-medium bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition-colors">
                    Notify
                </button>
                @endif
                <button wire:click="seat({{ $entry->id }})"
                        class="px-3 py-1.5 text-xs font-medium bg-green-50 hover:bg-green-100 text-green-700 rounded-lg transition-colors">
                    Seat
                </button>
                <button wire:click="cancel({{ $entry->id }})"
                        class="px-3 py-1.5 text-xs font-medium bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition-colors">
                    Cancel
                </button>
            </div>
        </div>
        @empty
        <div class="py-16 text-center text-stone-400">
            <div class="text-4xl mb-3">🎉</div>
            <p>No one waiting right now</p>
        </div>
        @endforelse
    </div>

    {{-- Recently Seated --}}
    @if($seated->count())
    <div class="bg-white rounded-2xl shadow-sm border border-stone-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-stone-100">
            <h2 class="font-semibold text-stone-800">Recently Seated</h2>
        </div>
        <div class="divide-y divide-stone-50">
            @foreach($seated as $entry)
            <div class="px-6 py-3 flex items-center justify-between">
                <div>
                    <span class="text-sm font-medium text-stone-700">{{ $entry->guest_name }}</span>
                    <span class="text-xs text-stone-400 ml-2">👥 {{ $entry->party_size }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-green-600">Seated {{ $entry->seated_at?->diffForHumans() }}</span>
                    <button wire:click="remove({{ $entry->id }})" class="text-xs text-stone-400 hover:text-red-500 transition-colors">✕</button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Add Guest Modal --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" wire:click="$set('showModal', false)"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md">
            <div class="p-6 border-b flex items-center justify-between">
                <h2 class="font-semibold text-stone-800">Add Guest to Waitlist</h2>
                <button wire:click="$set('showModal', false)" class="p-2 hover:bg-stone-100 rounded-xl text-stone-400">✕</button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-1.5">Guest Name *</label>
                    <input wire:model="guestName" type="text" class="w-full px-4 py-2.5 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                    @error('guestName') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-1.5">Phone *</label>
                    <input wire:model="phone" type="tel" class="w-full px-4 py-2.5 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                    @error('phone') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-1.5">Email (optional)</label>
                    <input wire:model="email" type="email" class="w-full px-4 py-2.5 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-1.5">Party Size *</label>
                    <input wire:model="partySize" type="number" min="1" max="20" class="w-full px-4 py-2.5 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                    @error('partySize') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-1.5">Notes (optional)</label>
                    <textarea wire:model="notes" rows="2" class="w-full px-4 py-2.5 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm resize-none"></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button wire:click="$set('showModal', false)" class="flex-1 py-2.5 border border-stone-200 rounded-xl text-sm font-medium text-stone-600 hover:bg-stone-50 transition-colors">Cancel</button>
                    <button wire:click="addGuest" class="flex-1 py-2.5 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-sm font-semibold transition-colors">Add Guest</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
