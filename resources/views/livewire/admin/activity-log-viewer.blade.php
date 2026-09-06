<div class="space-y-6">
    <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
        <h1 class="text-xl font-semibold text-stone-800">Activity Log</h1>
        <div class="flex flex-wrap gap-3">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search description or staff..." class="px-4 py-2 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 w-56">
            <select wire:model.live="actionFilter" class="px-3 py-2 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white">
                <option value="">All Actions</option>
                @foreach($actions as $action)
                <option value="{{ $action }}">{{ ucfirst(str_replace(['.', '_'], [' — ', ' '], $action)) }}</option>
                @endforeach
            </select>
            <input wire:model.live="dateFrom" type="date" class="px-3 py-2 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            <input wire:model.live="dateTo" type="date" class="px-3 py-2 border border-stone-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
        </div>
    </div>

    <p class="text-xs text-stone-400">This log covers sensitive actions only (payments, refunds, order cancellations, user/role changes, delivery-rider document access) — it is not a full record of every change in the system, and entries cannot be edited or deleted.</p>

    <div class="bg-white rounded-2xl border border-stone-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-stone-50 border-b border-stone-200 text-stone-500 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left">When</th>
                        <th class="px-4 py-3 text-left">Action</th>
                        <th class="px-4 py-3 text-left">Description</th>
                        <th class="px-4 py-3 text-left">By</th>
                        <th class="px-4 py-3 text-left">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($logs as $log)
                    <tr class="hover:bg-stone-50 transition-colors align-top">
                        <td class="px-4 py-3 text-stone-400 text-xs whitespace-nowrap">{{ $log->created_at->format('d M Y, H:i') }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-stone-100 text-stone-600 whitespace-nowrap">
                                {{ str_replace('_', ' ', $log->action) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-stone-700">
                            {{ $log->description }}
                            @if($log->properties)
                            <div class="text-xs text-stone-400 mt-1">
                                @foreach($log->properties as $key => $value)
                                    @if($value !== null && $value !== '')
                                    <span class="mr-2">{{ str_replace('_', ' ', $key) }}: <strong>{{ is_scalar($value) ? $value : json_encode($value) }}</strong></span>
                                    @endif
                                @endforeach
                            </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-stone-600 whitespace-nowrap">{{ $log->causer_name ?? 'System' }}</td>
                        <td class="px-4 py-3 text-stone-400 text-xs font-mono whitespace-nowrap">{{ $log->ip_address }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center text-stone-400 text-sm">No activity recorded yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-stone-100">{{ $logs->links() }}</div>
    </div>
</div>
