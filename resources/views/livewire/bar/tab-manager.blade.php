<div class="flex gap-4 h-[calc(100vh-88px)]" x-data>

    {{-- ── Left: Tabs panel ──────────────────────────────────────────────── --}}
    <div class="w-72 flex-shrink-0 flex flex-col gap-3">

        {{-- Open Tab CTA --}}
        <button wire:click="openNewTabModal"
                class="w-full py-3 bg-amber-600 hover:bg-amber-500 active:bg-amber-700
                       text-white font-bold rounded-2xl flex items-center justify-center gap-2
                       transition-all shadow-lg shadow-amber-900/30 hover:shadow-amber-900/50 hover:-translate-y-px">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Open Tab
        </button>

        {{-- Tab count label --}}
        @if($this->openTabs->isNotEmpty())
        <div class="flex items-center justify-between px-1">
            <span class="text-xs text-stone-500 uppercase tracking-wider font-semibold">Open Tabs</span>
            <span class="text-xs bg-amber-600/20 text-amber-400 font-bold px-2 py-0.5 rounded-full">
                {{ $this->openTabs->count() }}
            </span>
        </div>
        @endif

        {{-- Tab list --}}
        <div class="flex-1 overflow-y-auto space-y-2 pr-0.5">
            @forelse($this->openTabs as $tab)
            <button wire:click="selectTab({{ $tab->id }})"
                    class="w-full text-left p-4 rounded-2xl border transition-all duration-150
                        {{ $activeTabId === $tab->id
                            ? 'border-amber-500/60 bg-amber-900/20 shadow-lg shadow-amber-900/10'
                            : 'border-stone-700/60 bg-stone-900/60 hover:border-stone-600 hover:bg-stone-900' }}">
                {{-- Active indicator --}}
                <div class="flex items-start gap-3">
                    <div class="mt-0.5 w-2 h-2 rounded-full flex-shrink-0
                        {{ $activeTabId === $tab->id ? 'bg-amber-400' : 'bg-stone-600' }}"></div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="font-semibold text-white text-sm truncate">{{ $tab->customer_name }}</span>
                            <span class="text-amber-400 font-bold text-sm flex-shrink-0">
                                K {{ number_format($tab->total, 0) }}
                            </span>
                        </div>
                        <div class="text-xs text-stone-500">
                            {{ $tab->items->count() }} drink{{ $tab->items->count() !== 1 ? 's' : '' }}
                            @if($tab->table)
                            <span class="mx-1">·</span>🪑 {{ $tab->table->number }}
                            @endif
                            <span class="mx-1">·</span>{{ $tab->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
            </button>
            @empty
            <div class="flex flex-col items-center justify-center py-16 text-stone-600">
                <div class="text-5xl mb-3 opacity-40">🍸</div>
                <p class="text-sm font-medium">No open tabs</p>
                <p class="text-xs text-stone-700 mt-1">Tap "Open Tab" to start one</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ── Right: Active tab workspace ────────────────────────────────────── --}}
    <div class="flex-1 flex gap-4 min-w-0">

        @if($this->activeTab)

        {{-- Tab order panel --}}
        <div class="flex-1 flex flex-col rounded-2xl border border-stone-700/50 overflow-hidden"
             style="background: rgba(28,25,23,0.7);">

            {{-- Tab header --}}
            <div class="px-5 py-4 border-b border-stone-800/70 flex items-center justify-between"
                 style="background: rgba(41,37,36,0.8);">
                <div>
                    <div class="flex items-center gap-2 mb-0.5">
                        <h2 class="font-bold text-lg text-white">{{ $this->activeTab->customer_name }}</h2>
                        @if($this->activeTab->table)
                        <span class="text-xs bg-stone-700 text-stone-300 px-2 py-0.5 rounded-full">
                            🪑 {{ $this->activeTab->table->number }}
                        </span>
                        @endif
                    </div>
                    <div class="text-xs text-stone-500">
                        Opened {{ $this->activeTab->created_at->format('h:i A') }}
                        · by {{ $this->activeTab->opener?->name ?? 'Staff' }}
                    </div>
                </div>
                <div class="flex gap-2">
                    <button wire:click="openCloseModal({{ $this->activeTab->id }})"
                            class="flex items-center gap-1.5 px-4 py-2.5 bg-green-600 hover:bg-green-500
                                   text-white text-sm font-semibold rounded-xl transition-all
                                   shadow-md shadow-green-900/30 hover:-translate-y-px">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Close Tab
                    </button>
                    <button wire:click="voidTab({{ $this->activeTab->id }})"
                            wire:confirm="Void this tab? All items will be removed."
                            class="px-3 py-2.5 border border-red-800/50 text-red-500 hover:bg-red-900/20
                                   text-sm rounded-xl transition-all">
                        Void
                    </button>
                </div>
            </div>

            {{-- Items list --}}
            <div class="flex-1 overflow-y-auto p-4 space-y-2">
                @forelse($this->activeTab->items as $item)
                <div class="flex items-center gap-3 rounded-xl px-4 py-3 border border-stone-700/40
                            bg-stone-800/40 hover:bg-stone-800/70 transition-colors group">
                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-stone-100 text-sm">{{ $item->menuItem?->name }}</div>
                        <div class="text-xs text-stone-500 mt-0.5">K {{ number_format($item->unit_price, 0) }} each</div>
                    </div>
                    {{-- Qty controls --}}
                    <div class="flex items-center gap-1.5">
                        <button wire:click="decrementItem({{ $item->id }})"
                                class="w-7 h-7 rounded-lg bg-stone-700/80 hover:bg-stone-600
                                       text-stone-300 flex items-center justify-center text-sm transition-colors">
                            −
                        </button>
                        <span class="w-7 text-center font-bold text-white tabular-nums">{{ $item->quantity }}</span>
                        <button wire:click="addDrink({{ $item->menu_item_id }})"
                                class="w-7 h-7 rounded-lg bg-stone-700/80 hover:bg-stone-600
                                       text-stone-300 flex items-center justify-center text-sm transition-colors">
                            +
                        </button>
                    </div>
                    <div class="w-20 text-right font-bold text-amber-400 tabular-nums">
                        K {{ number_format($item->subtotal, 0) }}
                    </div>
                    <button wire:click="removeItem({{ $item->id }})"
                            class="text-stone-600 hover:text-red-400 transition-colors opacity-0 group-hover:opacity-100 ml-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center h-full py-16 text-stone-600">
                    <div class="text-4xl mb-3 opacity-40">🍹</div>
                    <p class="text-sm font-medium">Tab is empty</p>
                    <p class="text-xs text-stone-700 mt-1">Add drinks from the menu →</p>
                </div>
                @endforelse
            </div>

            {{-- Totals footer --}}
            <div class="px-5 py-4 border-t border-stone-800/70 space-y-2"
                 style="background: rgba(41,37,36,0.8);">
                <div class="flex justify-between text-xs text-stone-500">
                    <span>Subtotal</span>
                    <span class="tabular-nums">K {{ number_format($this->activeTab->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-xs text-stone-500">
                    <span>VAT (16%)</span>
                    <span class="tabular-nums">K {{ number_format($this->activeTab->tax, 2) }}</span>
                </div>
                <div class="flex justify-between items-baseline pt-2 border-t border-stone-700/50">
                    <span class="font-bold text-stone-200">Total</span>
                    <span class="font-bold text-2xl text-amber-400 tabular-nums">
                        K {{ number_format($this->activeTab->total, 2) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Drink menu panel --}}
        <div class="w-60 flex-shrink-0 flex flex-col rounded-2xl border border-stone-700/50 overflow-hidden"
             style="background: rgba(28,25,23,0.7);">
            {{-- Panel header --}}
            <div class="px-3 pt-3 pb-2 border-b border-stone-800/70">
                <p class="text-xs text-stone-500 uppercase tracking-wider font-semibold mb-2 px-1">Drinks Menu</p>
                <input wire:model.live.debounce.200ms="drinkSearch" type="text"
                       placeholder="Search…"
                       class="w-full px-3 py-2 bg-stone-800/80 border border-stone-700/60 rounded-xl
                              text-sm text-white placeholder-stone-600
                              focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-600/50 transition-all">
            </div>
            <div class="flex-1 overflow-y-auto p-2.5 space-y-1.5">
                @forelse($this->drinkMenu as $drink)
                <button wire:click="addDrink({{ $drink->id }})"
                        class="w-full text-left px-3 py-2.5 rounded-xl border border-transparent
                               hover:border-amber-600/40 hover:bg-amber-900/10
                               transition-all group">
                    <div class="font-medium text-sm text-stone-200 group-hover:text-amber-300 transition-colors leading-tight">
                        {{ $drink->name }}
                    </div>
                    <div class="text-amber-500 text-xs font-bold mt-0.5 tabular-nums">
                        K {{ number_format($drink->effective_price, 0) }}
                    </div>
                </button>
                @empty
                <div class="text-center text-stone-600 py-8 text-xs">No drinks found</div>
                @endforelse
            </div>
        </div>

        @else

        {{-- Empty state --}}
        <div class="flex-1 flex items-center justify-center">
            <div class="text-center">
                <div class="w-24 h-24 rounded-3xl bg-stone-800/50 border border-stone-700/40
                            flex items-center justify-center text-4xl mx-auto mb-5">
                    🍸
                </div>
                <h3 class="text-stone-300 font-semibold text-lg mb-1">No tab selected</h3>
                <p class="text-stone-600 text-sm">Open a new tab or select one from the left</p>
                <button wire:click="openNewTabModal"
                        class="mt-5 px-5 py-2.5 bg-amber-600 hover:bg-amber-500 text-white text-sm
                               font-semibold rounded-xl transition-all shadow-lg shadow-amber-900/20">
                    + Open Tab
                </button>
            </div>
        </div>

        @endif
    </div>

    {{-- ── New Tab Modal ────────────────────────────────────────────────── --}}
    @if($showNewTab)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/75 backdrop-blur-sm" wire:click="$set('showNewTab', false)"></div>
        <div class="relative rounded-3xl shadow-2xl w-full max-w-sm border border-stone-700/60 overflow-hidden"
             style="background: #211d1a;">
            <div class="h-[3px] bg-gradient-to-r from-amber-600 via-amber-400 to-amber-600"></div>
            <div class="p-5 border-b border-stone-800/60 flex items-center justify-between">
                <h3 class="font-bold text-white">Open New Tab</h3>
                <button wire:click="$set('showNewTab', false)"
                        class="w-7 h-7 rounded-lg bg-stone-800 hover:bg-stone-700 text-stone-400 hover:text-white
                               flex items-center justify-center text-sm transition-colors">✕</button>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-stone-400 uppercase tracking-wider mb-2">
                        Customer / Group Name *
                    </label>
                    <input wire:model="tabName" type="text" placeholder="e.g. John, Table 5 Group…"
                           class="w-full px-4 py-3 bg-stone-800/80 border border-stone-700/60 rounded-xl
                                  text-white placeholder-stone-600 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-600 transition-all">
                    @error('tabName') <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-stone-400 uppercase tracking-wider mb-2">
                        Bar Table (optional)
                    </label>
                    <select wire:model="tabTableId"
                            class="w-full px-4 py-3 bg-stone-800/80 border border-stone-700/60 rounded-xl
                                   text-white text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition-all">
                        <option value="">— No specific table —</option>
                        @foreach($this->barTables as $t)
                        <option value="{{ $t->id }}">{{ $t->number }} ({{ $t->capacity }} seats)</option>
                        @endforeach
                    </select>
                </div>
                <button wire:click="createTab"
                        class="w-full py-3 bg-amber-600 hover:bg-amber-500 text-white font-bold
                               rounded-xl transition-all shadow-lg shadow-amber-900/20 hover:-translate-y-px">
                    Open Tab
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Close Tab Modal ─────────────────────────────────────────────── --}}
    @if($showCloseModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/75 backdrop-blur-sm" wire:click="$set('showCloseModal', false)"></div>
        @php $closingTab = $closingTabId ? \App\Models\BarTab::with('items.menuItem')->find($closingTabId) : null; @endphp
        @if($closingTab)
        <div class="relative rounded-3xl shadow-2xl w-full max-w-sm border border-stone-700/60 overflow-hidden"
             style="background: #211d1a;">
            <div class="h-[3px] bg-gradient-to-r from-green-700 via-green-400 to-green-700"></div>
            <div class="p-5 border-b border-stone-800/60 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-white">Close Tab</h3>
                    <p class="text-xs text-stone-500 mt-0.5">{{ $closingTab->customer_name }}</p>
                </div>
                <button wire:click="$set('showCloseModal', false)"
                        class="w-7 h-7 rounded-lg bg-stone-800 hover:bg-stone-700 text-stone-400 hover:text-white
                               flex items-center justify-center text-sm transition-colors">✕</button>
            </div>
            <div class="p-5 space-y-4">
                {{-- Bill summary --}}
                <div class="rounded-2xl border border-stone-700/50 overflow-hidden">
                    <div class="px-4 py-2 bg-stone-800/50 border-b border-stone-700/40">
                        <span class="text-xs text-stone-500 font-semibold uppercase tracking-wider">Bill Summary</span>
                    </div>
                    <div class="p-4 space-y-2">
                        @foreach($closingTab->items as $i)
                        <div class="flex justify-between text-sm">
                            <span class="text-stone-400">{{ $i->quantity }}× {{ $i->menuItem?->name }}</span>
                            <span class="text-stone-300 tabular-nums">K {{ number_format($i->subtotal, 0) }}</span>
                        </div>
                        @endforeach
                        <div class="pt-2 mt-1 border-t border-stone-700/50 flex justify-between font-bold text-base">
                            <span class="text-stone-200">Total</span>
                            <span class="text-amber-400 tabular-nums">K {{ number_format($closingTab->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Payment method --}}
                <div>
                    <label class="block text-xs font-semibold text-stone-400 uppercase tracking-wider mb-2">
                        Payment Method
                    </label>
                    <select wire:model="closePayment"
                            class="w-full px-4 py-3 bg-stone-800/80 border border-stone-700/60 rounded-xl
                                   text-white text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition-all">
                        <option value="cash">💵 Cash</option>
                        <option value="card">💳 Card</option>
                        <option value="airtel_money">📱 Airtel Money</option>
                        <option value="mtn_momo">📱 MTN Money</option>
                        <option value="zamtel_kwacha">📱 Zamtel Kwacha</option>
                        <option value="zampay">📱 ZamPay</option>
                        <option value="bank_transfer">🏦 Bank Transfer</option>
                    </select>
                </div>

                <button wire:click="closeTab"
                        class="w-full py-3.5 bg-green-600 hover:bg-green-500 text-white font-bold rounded-xl
                               transition-all shadow-lg shadow-green-900/20 hover:-translate-y-px flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Confirm — K {{ number_format($closingTab->total, 2) }}
                </button>
            </div>
        </div>
        @endif
    </div>
    @endif

</div>
