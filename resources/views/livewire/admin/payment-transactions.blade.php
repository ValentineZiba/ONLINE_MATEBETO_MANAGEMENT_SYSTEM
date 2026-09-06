<div class="space-y-6">
    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">✅ {{ session('success') }}</div>
    @endif

    {{-- Header + Filters --}}
    <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
        <h1 class="text-xl font-semibold text-stone-800">Payment Transactions</h1>
        <div class="flex flex-wrap gap-3">
            <div class="relative">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by reference..." class="pl-4 pr-4 py-2 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 w-52">
            </div>
            <select wire:model.live="gatewayFilter" class="px-3 py-2 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white">
                <option value="">All Gateways</option>
                <option value="cash">Cash</option>
                <option value="card">Card</option>
                <option value="airtel_money">Airtel Money</option>
                <option value="mtn_momo">MTN Money</option>
                <option value="zamtel_kwacha">Zamtel Kwacha</option>
                <option value="zampay">ZamPay</option>
                <option value="bank_transfer">Bank Transfer</option>
                <option value="flutterwave">Flutterwave</option>
            </select>
            <select wire:model.live="statusFilter" class="px-3 py-2 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white">
                <option value="">All Statuses</option>
                @foreach(['pending', 'processing', 'succeeded', 'failed', 'cancelled'] as $s)
                <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-stone-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-stone-50 border-b border-stone-200 text-stone-500 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left">Reference</th>
                        <th class="px-4 py-3 text-left">For</th>
                        <th class="px-4 py-3 text-left">Gateway</th>
                        <th class="px-4 py-3 text-left">Type</th>
                        <th class="px-4 py-3 text-right">Amount</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Initiated By</th>
                        <th class="px-4 py-3 text-left">Date</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($transactions as $txn)
                    <tr class="hover:bg-stone-50 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs text-stone-600">{{ $txn->reference }}</td>
                        <td class="px-4 py-3 text-stone-600">
                            @if($txn->transactionable instanceof \App\Models\Order)
                                Order #{{ $txn->transactionable->order_number }}
                            @elseif($txn->transactionable instanceof \App\Models\BarTab)
                                Bar Tab · {{ $txn->transactionable->customer_name }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3 text-stone-600 capitalize">{{ str_replace('_', ' ', $txn->gateway) }}</td>
                        <td class="px-4 py-3 text-stone-600 capitalize">{{ $txn->type }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-stone-800">K {{ number_format($txn->amount, 2) }}</td>
                        <td class="px-4 py-3">
                            @php
                            $statusColors = [
                                'succeeded' => 'bg-green-100 text-green-700',
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'processing' => 'bg-blue-100 text-blue-700',
                                'failed' => 'bg-red-100 text-red-700',
                                'cancelled' => 'bg-stone-200 text-stone-600',
                            ];
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $statusColors[$txn->status] ?? 'bg-stone-100 text-stone-600' }}">
                                {{ ucfirst($txn->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-stone-500 text-xs">{{ $txn->initiator?->name ?? 'Customer' }}</td>
                        <td class="px-4 py-3 text-stone-400 text-xs whitespace-nowrap">{{ $txn->created_at->format('d M, H:i') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1 justify-end">
                                <button wire:click="view({{ $txn->id }})" title="View details" class="p-1.5 rounded-lg text-stone-400 hover:text-amber-600 hover:bg-amber-50 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                                @if(in_array($txn->status, ['pending', 'processing']))
                                <button wire:click="verifyNow({{ $txn->id }})" title="Verify now" class="p-1.5 rounded-lg text-stone-400 hover:text-blue-600 hover:bg-blue-50 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-12 text-center text-stone-400 text-sm">No payment transactions yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-stone-100">{{ $transactions->links() }}</div>
    </div>

    {{-- Detail drawer --}}
    @if($viewing)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" wire:click="closeView"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg p-6 max-h-[85vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-semibold text-stone-800">Transaction {{ $viewing->reference }}</h2>
                <button wire:click="closeView" class="p-2 hover:bg-stone-100 rounded-xl text-stone-400">✕</button>
            </div>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-stone-500">Gateway</span><span class="font-medium text-stone-800 capitalize">{{ str_replace('_', ' ', $viewing->gateway) }}</span></div>
                <div class="flex justify-between"><span class="text-stone-500">Type</span><span class="font-medium text-stone-800 capitalize">{{ $viewing->type }}</span></div>
                <div class="flex justify-between"><span class="text-stone-500">Status</span><span class="font-medium text-stone-800 capitalize">{{ $viewing->status }}</span></div>
                <div class="flex justify-between"><span class="text-stone-500">Amount</span><span class="font-medium text-stone-800">K {{ number_format($viewing->amount, 2) }} {{ $viewing->currency }}</span></div>
                @if($viewing->phone_number)
                <div class="flex justify-between"><span class="text-stone-500">Phone</span><span class="font-medium text-stone-800">{{ $viewing->phone_number }}</span></div>
                @endif
                @if($viewing->gateway_reference)
                <div class="flex justify-between"><span class="text-stone-500">Gateway Reference</span><span class="font-mono text-xs text-stone-800">{{ $viewing->gateway_reference }}</span></div>
                @endif
                @if($viewing->failure_reason)
                <div class="flex justify-between"><span class="text-stone-500">Failure Reason</span><span class="font-medium text-red-600">{{ $viewing->failure_reason }}</span></div>
                @endif
                <div class="flex justify-between"><span class="text-stone-500">Initiated By</span><span class="font-medium text-stone-800">{{ $viewing->initiator?->name ?? 'Customer (self-serve)' }}</span></div>
                <div class="flex justify-between"><span class="text-stone-500">Created</span><span class="font-medium text-stone-800">{{ $viewing->created_at->format('d M Y, H:i') }}</span></div>
                @if($viewing->confirmed_at)
                <div class="flex justify-between"><span class="text-stone-500">Confirmed</span><span class="font-medium text-stone-800">{{ $viewing->confirmed_at->format('d M Y, H:i') }}</span></div>
                @endif
                @if($viewing->meta)
                <div class="pt-2 border-t border-stone-100">
                    <div class="text-stone-500 mb-1">Meta</div>
                    <pre class="bg-stone-50 rounded-xl p-3 text-xs overflow-x-auto text-stone-600">{{ json_encode($viewing->meta, JSON_PRETTY_PRINT) }}</pre>
                </div>
                @endif
                @if($viewing->gateway_response)
                <div class="pt-2 border-t border-stone-100">
                    <div class="text-stone-500 mb-1">Gateway Response</div>
                    <pre class="bg-stone-50 rounded-xl p-3 text-xs overflow-x-auto text-stone-600">{{ json_encode($viewing->gateway_response, JSON_PRETTY_PRINT) }}</pre>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
