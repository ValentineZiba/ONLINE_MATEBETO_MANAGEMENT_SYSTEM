<div>
    <div class="bg-stone-900 py-16 text-center">
        <h1 class="font-display text-4xl md:text-5xl font-bold text-white mb-3">Make a Reservation</h1>
        <p class="text-stone-400 max-w-xl mx-auto">Book your table and enjoy a memorable dining experience at Matebeto</p>
    </div>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-16">
        @if($submitted)
        <div class="text-center py-12 bg-white rounded-3xl shadow-sm border border-stone-100">
            <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="text-5xl">🎉</span>
            </div>
            <h2 class="font-display text-3xl font-bold text-stone-800 mb-3">Reservation Confirmed!</h2>
            <p class="text-stone-500 mb-6 max-w-md mx-auto">Thank you, {{ $guestName }}! Your table has been reserved. We look forward to seeing you.</p>
            <div class="inline-block bg-amber-50 border border-amber-200 rounded-2xl px-8 py-5 mb-8">
                <div class="text-xs text-amber-600 uppercase tracking-widest mb-2">Confirmation Code</div>
                <div class="text-4xl font-bold text-amber-700 font-mono">{{ $confirmationCode }}</div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 max-w-lg mx-auto mb-8">
                <div class="bg-stone-50 rounded-xl p-3 text-center">
                    <div class="text-xs text-stone-400 mb-1">Date</div>
                    <div class="font-semibold text-stone-700 text-sm">{{ \Carbon\Carbon::parse($reservationDate)->format('M d, Y') }}</div>
                </div>
                <div class="bg-stone-50 rounded-xl p-3 text-center">
                    <div class="text-xs text-stone-400 mb-1">Time</div>
                    <div class="font-semibold text-stone-700 text-sm">{{ $reservationTime }}</div>
                </div>
                <div class="bg-stone-50 rounded-xl p-3 text-center">
                    <div class="text-xs text-stone-400 mb-1">Guests</div>
                    <div class="font-semibold text-stone-700 text-sm">{{ $partySize }} pax</div>
                </div>
                <div class="bg-stone-50 rounded-xl p-3 text-center">
                    <div class="text-xs text-stone-400 mb-1">Status</div>
                    <div class="font-semibold text-amber-600 text-sm">Pending</div>
                </div>
            </div>
            <p class="text-stone-400 text-sm mb-6">A confirmation will be sent to <strong>{{ $guestEmail }}</strong></p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <button wire:click="$set('submitted', false)" class="px-8 py-3 border-2 border-stone-300 hover:border-stone-400 text-stone-600 font-semibold rounded-xl transition-colors">New Reservation</button>
                <a href="{{ route('menu') }}" class="px-8 py-3 bg-amber-600 hover:bg-amber-500 text-white font-semibold rounded-xl transition-colors">Order Food →</a>
            </div>
        </div>
        @else
        <div class="bg-white rounded-3xl shadow-sm border border-stone-100 overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-3">
                {{-- Form --}}
                <div class="lg:col-span-2 p-8">
                    <h2 class="font-display text-2xl font-bold text-stone-800 mb-6">Reservation Details</h2>
                    <form wire:submit="submit" class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-stone-700 mb-2">Full Name *</label>
                                <input wire:model="guestName" type="text" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="Your full name">
                                @error('guestName') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-stone-700 mb-2">Phone *</label>
                                <input wire:model="guestPhone" type="tel" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="+260...">
                                @error('guestPhone') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-stone-700 mb-2">Email Address *</label>
                            <input wire:model="guestEmail" type="email" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="your@email.com">
                            @error('guestEmail') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-stone-700 mb-2">Date *</label>
                                <input wire:model="reservationDate" type="date" min="{{ now()->addDay()->format('Y-m-d') }}" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                                @error('reservationDate') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-stone-700 mb-2">Time *</label>
                                <select wire:model="reservationTime" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white">
                                    @foreach($this->availableTimes as $time)
                                    <option value="{{ $time }}">{{ $time }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-stone-700 mb-2">Party Size *</label>
                                <select wire:model="partySize" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white">
                                    @for($i = 1; $i <= 20; $i++)
                                    <option value="{{ $i }}">{{ $i }} {{ $i === 1 ? 'person' : 'people' }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-stone-700 mb-2">Occasion (Optional)</label>
                            <select wire:model="occasion" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white">
                                <option value="">-- None --</option>
                                @foreach(['Birthday', 'Anniversary', 'Business Dinner', 'Date Night', 'Family Gathering', 'Graduation', 'Engagement', 'Other'] as $occ)
                                <option value="{{ $occ }}">{{ $occ }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-stone-700 mb-2">Special Requests</label>
                            <textarea wire:model="specialRequests" rows="3" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 resize-none" placeholder="Dietary requirements, seating preferences, allergies..."></textarea>
                        </div>
                        <button type="submit" wire:loading.attr="disabled"
                                class="w-full py-4 bg-amber-600 hover:bg-amber-500 text-white font-bold rounded-xl text-lg transition-colors shadow-lg shadow-amber-900/20">
                            <span wire:loading.remove>Confirm Reservation 🗓️</span>
                            <span wire:loading>Processing...</span>
                        </button>
                    </form>
                </div>

                {{-- Info sidebar --}}
                <div class="bg-stone-900 p-8 flex flex-col gap-6">
                    <div>
                        <h3 class="text-white font-display font-semibold text-lg mb-4">Opening Hours</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between text-stone-300"><span>Mon – Fri</span><span class="text-amber-400">7 AM – 11 PM</span></div>
                            <div class="flex justify-between text-stone-300"><span>Saturday</span><span class="text-amber-400">8 AM – 12 AM</span></div>
                            <div class="flex justify-between text-stone-300"><span>Sunday</span><span class="text-amber-400">9 AM – 10 PM</span></div>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-white font-display font-semibold text-lg mb-4">Dining Areas</h3>
                        <div class="space-y-3">
                            @foreach([['Indoor', 'Elegant air-conditioned dining', '🏠'], ['Outdoor', 'Lush garden terrace seating', '🌿'], ['Bar', 'Vibrant cocktail lounge', '🍸'], ['Private', 'Exclusive events up to 20 guests', '🎭']] as [$area, $desc, $icon])
                            <div class="flex gap-3 text-sm">
                                <span class="text-2xl">{{ $icon }}</span>
                                <div>
                                    <div class="text-white font-medium">{{ $area }}</div>
                                    <div class="text-stone-400 text-xs">{{ $desc }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="mt-auto pt-4 border-t border-stone-700">
                        <p class="text-stone-400 text-xs leading-relaxed">For parties larger than 20 or same-day bookings, please call us directly.</p>
                        <a href="tel:+260977123456" class="mt-3 flex items-center gap-2 text-amber-400 hover:text-amber-300 transition-colors text-sm font-medium">
                            📞 +260 977 123 456
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
