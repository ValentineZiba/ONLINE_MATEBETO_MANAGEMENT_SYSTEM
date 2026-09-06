<div class="max-w-3xl mx-auto space-y-5">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-slate-800">My Reservations</h2>
        <a href="{{ route('reservations') }}" class="px-5 py-2 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-sm font-semibold transition-colors">
            + New Reservation
        </a>
    </div>

    @forelse($reservations as $reservation)
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 flex flex-wrap gap-4 items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="h-14 w-14 rounded-2xl flex items-center justify-center text-2xl
                    {{ match($reservation->status) {
                        'confirmed' => 'bg-blue-50',
                        'seated' => 'bg-green-50',
                        'completed' => 'bg-slate-50',
                        'cancelled', 'no_show' => 'bg-red-50',
                        default => 'bg-amber-50'
                    } }}">
                    {{ match($reservation->status) { 'confirmed' => '✅', 'seated' => '🪑', 'completed' => '🎉', 'cancelled' => '❌', 'no_show' => '🚫', default => '⏳' } }}
                </div>
                <div>
                    <div class="font-semibold text-slate-800">{{ $reservation->reservation_date->format('l, M d Y') }}</div>
                    <div class="text-slate-500 text-sm">{{ \Carbon\Carbon::parse($reservation->reservation_time)->format('h:i A') }} • {{ $reservation->party_size }} guests</div>
                    @if($reservation->occasion)
                    <div class="text-amber-600 text-xs mt-1">🎉 {{ ucfirst($reservation->occasion) }}</div>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <span class="text-xs px-3 py-1 rounded-full font-medium
                        {{ match($reservation->status) {
                            'pending' => 'bg-yellow-100 text-yellow-700',
                            'confirmed' => 'bg-blue-100 text-blue-700',
                            'seated' => 'bg-green-100 text-green-700',
                            'completed' => 'bg-slate-100 text-slate-600',
                            'cancelled' => 'bg-red-100 text-red-600',
                            'no_show' => 'bg-orange-100 text-orange-700',
                            default => 'bg-slate-100 text-slate-600',
                        } }}">{{ ucfirst(str_replace('_', ' ', $reservation->status)) }}</span>
                    <div class="text-xs text-slate-400 mt-1.5">Ref: {{ $reservation->confirmation_code }}</div>
                </div>
                @if($reservation->status === 'pending')
                <button wire:click="cancel({{ $reservation->id }})" wire:confirm="Cancel this reservation?"
                        class="px-4 py-2 text-sm border border-red-200 hover:bg-red-50 text-red-600 rounded-xl transition-colors">
                    Cancel
                </button>
                @endif
            </div>
        </div>
        @if($reservation->special_requests)
        <div class="px-6 pb-5">
            <div class="bg-amber-50 border border-amber-100 rounded-xl p-3 text-sm text-amber-800">
                <span class="font-medium">Special requests:</span> {{ $reservation->special_requests }}
            </div>
        </div>
        @endif
    </div>
    @empty
    <div class="text-center py-24">
        <div class="text-6xl mb-4">📅</div>
        <h3 class="text-xl font-semibold text-slate-700">No reservations yet</h3>
        <p class="text-slate-400 mt-2 mb-6">Reserve a table for your next visit</p>
        <a href="{{ route('reservations') }}" class="inline-block px-8 py-3 bg-amber-600 hover:bg-amber-500 text-white font-semibold rounded-xl transition-colors">Make a Reservation</a>
    </div>
    @endforelse

    <div class="mt-4">{{ $reservations->links() }}</div>
</div>
