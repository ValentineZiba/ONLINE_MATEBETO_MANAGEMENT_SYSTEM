<div class="space-y-6">
    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @foreach([['Total', $stats['total'], 'bg-stone-100 text-stone-700'], ['Available', $stats['available'], 'bg-green-100 text-green-700'], ['Occupied', $stats['occupied'], 'bg-red-100 text-red-700'], ['Reserved', $stats['reserved'], 'bg-yellow-100 text-yellow-700']] as [$label, $count, $cls])
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-stone-100 text-center">
            <div class="text-3xl font-bold {{ explode(' ', $cls)[1] }}">{{ $count }}</div>
            <div class="text-sm text-stone-500 mt-1">{{ $label }}</div>
        </div>
        @endforeach
    </div>

    <div class="flex justify-end">
        <button wire:click="openCreate" class="px-5 py-2 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-sm font-semibold transition-colors flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Table
        </button>
    </div>

    {{-- Floor Plan Grid --}}
    @foreach(['indoor' => 'Indoor', 'outdoor' => 'Outdoor', 'bar' => 'Bar', 'private' => 'Private Events'] as $loc => $locLabel)
    @php $locTables = $tables->where('location', $loc); @endphp
    @if($locTables->isNotEmpty())
    <div class="bg-white rounded-2xl shadow-sm border border-stone-100 p-6">
        <h3 class="font-semibold text-stone-700 mb-4 flex items-center gap-2">
            {{ match($loc) { 'indoor' => '🏠', 'outdoor' => '🌿', 'bar' => '🍸', 'private' => '🎭', default => '🪑' } }}
            {{ $locLabel }}
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            @foreach($locTables as $table)
            <div class="relative group rounded-2xl border-2 p-4 text-center transition-all
                {{ match($table->status) {
                    'available' => 'border-green-300 bg-green-50 hover:border-green-400',
                    'occupied' => 'border-red-300 bg-red-50',
                    'reserved' => 'border-yellow-300 bg-yellow-50',
                    'cleaning' => 'border-blue-300 bg-blue-50',
                    'inactive' => 'border-stone-200 bg-stone-50 opacity-50',
                    default => 'border-stone-200 bg-stone-50',
                } }}">
                <div class="text-2xl mb-1">{{ $table->status === 'available' ? '🪑' : ($table->status === 'occupied' ? '👥' : ($table->status === 'reserved' ? '📅' : ($table->status === 'cleaning' ? '🧹' : '🚫'))) }}</div>
                <div class="font-bold text-stone-800 text-lg">{{ $table->number }}</div>
                <div class="text-xs text-stone-500 mb-2">{{ $table->capacity }} seats</div>
                <span class="text-xs font-medium px-2 py-0.5 rounded-full
                    {{ match($table->status) { 'available' => 'bg-green-200 text-green-800', 'occupied' => 'bg-red-200 text-red-800', 'reserved' => 'bg-yellow-200 text-yellow-800', 'cleaning' => 'bg-blue-200 text-blue-800', default => 'bg-stone-200 text-stone-600' } }}">
                    {{ ucfirst($table->status) }}
                </span>
                <div class="mt-2 opacity-0 group-hover:opacity-100 transition-opacity flex flex-wrap gap-1 justify-center">
                    @foreach(['available', 'occupied', 'reserved', 'cleaning'] as $s)
                    @if($s !== $table->status)
                    <button wire:click="setStatus({{ $table->id }}, '{{ $s }}')" class="text-xs px-2 py-0.5 bg-stone-700 hover:bg-stone-600 text-white rounded transition-colors">{{ ucfirst($s) }}</button>
                    @endif
                    @endforeach
                    <button wire:click="openEdit({{ $table->id }})" class="text-xs px-2 py-0.5 bg-amber-600 hover:bg-amber-500 text-white rounded transition-colors">Edit</button>
                    <button wire:click="openQr({{ $table->id }})" title="QR Code" class="text-xs px-2 py-0.5 bg-stone-600 hover:bg-stone-500 text-white rounded transition-colors">QR</button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    @endforeach

    {{-- QR Code Modal --}}
    @if($showQrModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data>
        <div class="absolute inset-0 bg-black/60" wire:click="$set('showQrModal', false)"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-sm">
            <div class="p-5 border-b border-stone-100 flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-stone-800">Table {{ $qrTableNumber }} — QR Code</h2>
                    <p class="text-xs text-stone-400 mt-0.5">Customers scan to order from their seat</p>
                </div>
                <button wire:click="$set('showQrModal', false)" class="p-2 hover:bg-stone-100 rounded-xl text-stone-400">✕</button>
            </div>

            <div class="p-6 flex flex-col items-center gap-5">
                {{-- QR code image from the show route --}}
                <div class="p-4 bg-amber-50 border-2 border-amber-200 rounded-2xl" id="qr-print-area">
                    <img src="{{ route('qr.show', $qrTableId) }}" alt="QR Code for Table {{ $qrTableNumber }}"
                         class="w-56 h-56" id="qr-img">
                    <div class="text-center mt-3">
                        <div class="font-display font-bold text-stone-800 text-lg">🍽️ Matebeto Restaurant</div>
                        <div class="text-stone-500 text-sm">Table {{ $qrTableNumber }} — Scan to Order</div>
                    </div>
                </div>

                {{-- URL preview --}}
                <div class="w-full bg-stone-50 rounded-xl px-3 py-2 text-xs text-stone-500 font-mono break-all text-center">
                    {{ $qrUrl }}
                </div>

                {{-- Actions --}}
                <div class="flex gap-3 w-full">
                    <button onclick="printQr()"
                            class="flex-1 py-2.5 bg-stone-800 hover:bg-stone-700 text-white text-sm font-semibold rounded-xl flex items-center justify-center gap-2 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Print
                    </button>
                    <a href="{{ route('qr.show', $qrTableId) }}" download="table-{{ $qrTableNumber }}-qr.svg"
                       class="flex-1 py-2.5 bg-amber-600 hover:bg-amber-500 text-white text-sm font-semibold rounded-xl flex items-center justify-center gap-2 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Download
                    </a>
                </div>
                <button wire:click="regenerateQr({{ $qrTableId }})"
                        wire:confirm="Regenerate QR? The old QR code will stop working."
                        class="text-xs text-stone-400 hover:text-red-500 transition-colors">
                    ↺ Regenerate QR code
                </button>
            </div>
        </div>
    </div>

    @once
    <style>
        @media print {
            body > *:not(#qr-print-frame) { display: none !important; }
            #qr-print-frame { display: block !important; }
        }
        #qr-print-frame { display: none; }
    </style>
    <script>
        function printQr() {
            var frame = document.getElementById('qr-print-frame');
            if (!frame) {
                frame = document.createElement('div');
                frame.id = 'qr-print-frame';
                frame.style.cssText = 'position:fixed;inset:0;background:white;display:flex;align-items:center;justify-content:center;z-index:9999;';
                document.body.appendChild(frame);
            }
            frame.innerHTML = document.getElementById('qr-print-area').outerHTML;
            frame.style.display = 'flex';
            window.print();
            frame.style.display = 'none';
        }
    </script>
    @endonce
    @endif

    {{-- Modal --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" wire:click="$set('showModal', false)"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md">
            <div class="p-6 border-b flex items-center justify-between">
                <h2 class="font-semibold text-stone-800">{{ $editing ? 'Edit Table' : 'Add Table' }}</h2>
                <button wire:click="$set('showModal', false)" class="p-2 hover:bg-stone-100 rounded-xl text-stone-500">✕</button>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-2">Table Number *</label>
                        <input wire:model="number" type="text" placeholder="T01" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                        @error('number') <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-2">Capacity *</label>
                        <input wire:model="capacity" type="number" min="1" max="50" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-2">Location</label>
                        <select wire:model="location" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white">
                            @foreach(['indoor', 'outdoor', 'bar', 'private'] as $l)
                            <option value="{{ $l }}">{{ ucfirst($l) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-stone-700 mb-2">Status</label>
                        <select wire:model="status" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white">
                            @foreach(['available', 'occupied', 'reserved', 'cleaning', 'inactive'] as $s)
                            <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-stone-700 mb-2">Notes</label>
                        <input wire:model="notes" type="text" class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500" placeholder="Window seat, corner table...">
                    </div>
                </div>
                <div class="flex gap-3">
                    <button wire:click="save" class="flex-1 py-3 bg-amber-600 hover:bg-amber-500 text-white font-semibold rounded-xl">{{ $editing ? 'Update' : 'Create' }}</button>
                    <button wire:click="$set('showModal', false)" class="px-6 py-3 border border-stone-200 hover:bg-stone-50 text-stone-600 rounded-xl">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
