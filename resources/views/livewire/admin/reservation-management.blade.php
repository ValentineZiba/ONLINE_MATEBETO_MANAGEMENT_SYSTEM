<div class="space-y-5">
    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @foreach([['Today Total', $stats['today_total'], 'slate'], ['Pending', $stats['pending'], 'yellow'], ['Confirmed', $stats['confirmed'], 'blue'], ['Seated', $stats['seated'], 'green']] as [$label, $count, $color])
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 text-center">
            <div class="text-3xl font-bold text-{{ $color }}-600">{{ $count }}</div>
            <div class="text-sm text-stone-500 mt-1">{{ $label }}</div>
        </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap gap-3">
        <div class="relative"><svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search..." class="pl-10 pr-4 py-2 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 w-48">
        </div>
        <input wire:model.live="dateFilter" type="date" class="px-3 py-2 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
        <select wire:model.live="statusFilter" class="px-3 py-2 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white">
            <option value="">All Statuses</option>
            @foreach(['pending','confirmed','seated','completed','cancelled','no_show'] as $s)
            <option value="{{ $s }}">{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
            @endforeach
        </select>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-stone-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead><tr class="bg-stone-50 text-left">
                    <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase">Code</th>
                    <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase">Guest</th>
                    <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase">Date & Time</th>
                    <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase">Party</th>
                    <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase">Table</th>
                    <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase">Occasion</th>
                    <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase">Status</th>
                    <th class="px-6 py-4 text-xs font-semibold text-stone-500 uppercase">Actions</th>
                </tr></thead>
                <tbody class="divide-y divide-stone-50">
                    @forelse($reservations as $res)
                    <tr class="hover:bg-stone-50 transition-colors">
                        <td class="px-6 py-4 font-mono text-sm font-semibold text-stone-800">{{ $res->confirmation_code }}</td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-stone-800 text-sm">{{ $res->guest_name }}</div>
                            <div class="text-stone-400 text-xs">{{ $res->guest_email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-stone-700">{{ $res->reservation_date->format('M d, Y') }}</div>
                            <div class="text-stone-400 text-xs">{{ \Carbon\Carbon::parse($res->reservation_time)->format('h:i A') }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $res->party_size }} guests</td>
                        <td class="px-6 py-4 text-sm text-stone-600">{{ $res->table?->number ?? '—' }}</td>
                        <td class="px-6 py-4 text-xs text-stone-500">{{ $res->occasion ?? '—' }}</td>
                        <td class="px-6 py-4">
                            <span class="text-xs px-2 py-1 rounded-full font-medium
                                {{ match($res->status) {
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'confirmed' => 'bg-blue-100 text-blue-700',
                                    'seated' => 'bg-green-100 text-green-700',
                                    'completed' => 'bg-stone-100 text-stone-600',
                                    'cancelled' => 'bg-red-100 text-red-600',
                                    'no_show' => 'bg-orange-100 text-orange-700',
                                    default => 'bg-stone-100 text-stone-600',
                                } }}">{{ ucfirst(str_replace('_', ' ', $res->status)) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <button wire:click="viewReservation({{ $res->id }})" class="text-sm text-amber-600 hover:text-amber-700 font-medium">Manage</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-16 text-stone-400">No reservations found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-stone-50">{{ $reservations->links() }}</div>
    </div>

    @if($showModal && $selectedReservation)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" wire:click="$set('showModal', false)"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b flex justify-between items-center sticky top-0 bg-white rounded-t-3xl">
                <h2 class="font-semibold text-stone-800">Reservation #{{ $selectedReservation->confirmation_code }}</h2>
                <button wire:click="$set('showModal', false)" class="p-2 hover:bg-stone-100 rounded-xl">✕</button>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-stone-50 rounded-xl p-3"><div class="text-xs text-stone-400 mb-1">Guest</div><div class="font-medium text-sm">{{ $selectedReservation->guest_name }}</div></div>
                    <div class="bg-stone-50 rounded-xl p-3"><div class="text-xs text-stone-400 mb-1">Phone</div><div class="font-medium text-sm">{{ $selectedReservation->guest_phone }}</div></div>
                    <div class="bg-stone-50 rounded-xl p-3"><div class="text-xs text-stone-400 mb-1">Date</div><div class="font-medium text-sm">{{ $selectedReservation->reservation_date->format('M d, Y') }}</div></div>
                    <div class="bg-stone-50 rounded-xl p-3"><div class="text-xs text-stone-400 mb-1">Time</div><div class="font-medium text-sm">{{ \Carbon\Carbon::parse($selectedReservation->reservation_time)->format('h:i A') }}</div></div>
                    <div class="bg-stone-50 rounded-xl p-3"><div class="text-xs text-stone-400 mb-1">Party</div><div class="font-medium text-sm">{{ $selectedReservation->party_size }} guests</div></div>
                    <div class="bg-stone-50 rounded-xl p-3"><div class="text-xs text-stone-400 mb-1">Occasion</div><div class="font-medium text-sm">{{ $selectedReservation->occasion ?? 'None' }}</div></div>
                </div>
                @if($selectedReservation->special_requests)
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-800"><strong>Requests:</strong> {{ $selectedReservation->special_requests }}</div>
                @endif

                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-2">Assign Table</label>
                    <div class="flex gap-2">
                        <select wire:model="assignTableId" class="flex-1 px-3 py-2 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white">
                            <option value="">No table</option>
                            @foreach($tables as $t)
                            <option value="{{ $t->id }}">{{ $t->number }} ({{ $t->capacity }} seats, {{ $t->location }})</option>
                            @endforeach
                        </select>
                        <button wire:click="assignTable" class="px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white text-sm rounded-xl transition-colors">Assign</button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-2">Update Status</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['confirmed', 'seated', 'completed', 'cancelled', 'no_show'] as $s)
                        <button wire:click="updateStatus({{ $selectedReservation->id }}, '{{ $s }}')"
                                class="px-4 py-2 text-sm font-medium rounded-xl border transition-colors {{ $selectedReservation->status === $s ? 'bg-amber-600 text-white border-amber-600' : ($s === 'cancelled' ? 'border-red-300 text-red-600 hover:bg-red-50' : 'border-stone-200 text-stone-600 hover:bg-stone-50') }}">
                            {{ ucfirst(str_replace('_', ' ', $s)) }}
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
