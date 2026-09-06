<div class="space-y-6">
    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">✅ {{ session('success') }}</div>
    @endif

    {{-- Week Navigator --}}
    <div class="bg-white rounded-2xl shadow-sm border border-stone-100 p-4 flex items-center justify-between">
        <button wire:click="prevWeek" class="p-2 hover:bg-stone-100 rounded-xl text-stone-500 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <div class="text-center">
            <h2 class="font-semibold text-stone-800">
                {{ \Carbon\Carbon::parse($weekStart)->format('M d') }} – {{ \Carbon\Carbon::parse($weekStart)->endOfWeek()->format('M d, Y') }}
            </h2>
            <p class="text-xs text-stone-400 mt-0.5">Week {{ \Carbon\Carbon::parse($weekStart)->weekOfYear }}</p>
        </div>
        <button wire:click="nextWeek" class="p-2 hover:bg-stone-100 rounded-xl text-stone-500 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>

    {{-- Weekly Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-7 gap-3">
        @foreach($days as $day)
        @php $dateKey = $day->format('Y-m-d'); $isToday = $day->isToday(); @endphp
        <div class="bg-white rounded-2xl shadow-sm border {{ $isToday ? 'border-amber-300' : 'border-stone-100' }} overflow-hidden">
            <div class="px-3 py-2 {{ $isToday ? 'bg-amber-600 text-white' : 'bg-stone-50 text-stone-600' }} text-center">
                <div class="text-xs font-semibold uppercase">{{ $day->format('D') }}</div>
                <div class="text-lg font-bold">{{ $day->format('d') }}</div>
            </div>
            <div class="p-2 space-y-1.5 min-h-[100px]">
                @if(isset($shifts[$dateKey]))
                    @foreach($shifts[$dateKey] as $shift)
                    <div class="rounded-lg p-2 text-xs
                        {{ $shift->status === 'completed' ? 'bg-stone-100 text-stone-400' :
                           ($shift->role === 'manager' ? 'bg-purple-50 text-purple-700 border border-purple-200' :
                           ($shift->role === 'kitchen' ? 'bg-orange-50 text-orange-700 border border-orange-200' :
                           ($shift->role === 'bar' ? 'bg-amber-50 text-amber-700 border border-amber-200' :
                            'bg-blue-50 text-blue-700 border border-blue-200'))) }}">
                        <div class="font-semibold truncate">{{ $shift->user->name }}</div>
                        <div class="mt-0.5 opacity-75">{{ $shift->start_time }} – {{ $shift->end_time }}</div>
                        <div class="flex items-center justify-between mt-1">
                            <span class="capitalize opacity-75">{{ $shift->role }}</span>
                            <div class="flex gap-1">
                                <button wire:click="openEdit({{ $shift->id }})" class="hover:opacity-100 opacity-60 transition-opacity">✏️</button>
                                <button wire:click="delete({{ $shift->id }})" wire:confirm="Delete this shift?" class="hover:opacity-100 opacity-60 transition-opacity">🗑️</button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
                <button wire:click="openCreate('{{ $dateKey }}')"
                        class="w-full py-1 border border-dashed border-stone-200 hover:border-amber-400 text-stone-300 hover:text-amber-500 rounded-lg text-xs transition-colors">
                    + Add
                </button>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Legend --}}
    <div class="flex flex-wrap gap-3 text-xs">
        <span class="px-3 py-1 rounded-full bg-purple-50 text-purple-700 border border-purple-200 font-medium">Manager</span>
        <span class="px-3 py-1 rounded-full bg-orange-50 text-orange-700 border border-orange-200 font-medium">Kitchen</span>
        <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200 font-medium">Waiter</span>
        <span class="px-3 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 font-medium">Bar</span>
        <span class="px-3 py-1 rounded-full bg-stone-100 text-stone-400 border border-stone-200 font-medium">Completed</span>
    </div>

    {{-- Shift Modal --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" wire:click="$set('showModal', false)"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md">
            <div class="p-6 border-b flex items-center justify-between">
                <h2 class="font-semibold text-stone-800">{{ $editId ? 'Edit Shift' : 'Add Shift' }}</h2>
                <button wire:click="$set('showModal', false)" class="p-2 hover:bg-stone-100 rounded-xl text-stone-400">✕</button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-1.5">Staff Member *</label>
                    <select wire:model="userId" class="w-full px-4 py-2.5 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm bg-white">
                        <option value="">Select staff member</option>
                        @foreach($staff as $s)
                        <option value="{{ $s->id }}">{{ $s->name }} ({{ ucfirst($s->role) }})</option>
                        @endforeach
                    </select>
                    @error('userId') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-1.5">Date *</label>
                    <input wire:model="date" type="date" class="w-full px-4 py-2.5 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                    @error('date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-1.5">Start Time *</label>
                        <input wire:model="startTime" type="time" class="w-full px-4 py-2.5 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-1.5">End Time *</label>
                        <input wire:model="endTime" type="time" class="w-full px-4 py-2.5 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                        @error('endTime') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-1.5">Role *</label>
                        <select wire:model="role" class="w-full px-4 py-2.5 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm bg-white">
                            <option value="manager">Manager</option>
                            <option value="kitchen">Kitchen</option>
                            <option value="waiter">Waiter</option>
                            <option value="bar">Bar</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-1.5">Status</label>
                        <select wire:model="status" class="w-full px-4 py-2.5 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm bg-white">
                            <option value="scheduled">Scheduled</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="absent">Absent</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-1.5">Notes</label>
                    <textarea wire:model="notes" rows="2" class="w-full px-4 py-2.5 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm resize-none"></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button wire:click="$set('showModal', false)" class="flex-1 py-2.5 border border-stone-200 rounded-xl text-sm font-medium text-stone-600 hover:bg-stone-50 transition-colors">Cancel</button>
                    <button wire:click="save" class="flex-1 py-2.5 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-sm font-semibold transition-colors">Save Shift</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
