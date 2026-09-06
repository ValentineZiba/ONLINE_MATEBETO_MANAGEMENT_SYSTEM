<div class="space-y-5">
    {{-- Filter tabs --}}
    <div class="flex gap-2 bg-stone-100 p-1 rounded-xl w-fit">
        @foreach(['all' => 'All', 'pending' => 'Pending', 'approved' => 'Approved'] as $val => $label)
        <button wire:click="$set('filter', '{{ $val }}')"
                class="px-4 py-1.5 text-sm rounded-lg font-medium transition-colors {{ $filter === $val ? 'bg-white text-stone-800 shadow-sm' : 'text-stone-500 hover:text-stone-700' }}">
            {{ $label }}
        </button>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-4">
        @forelse($reviews as $review)
        <div class="bg-white rounded-2xl shadow-sm border border-stone-100 p-6">
            <div class="flex flex-col sm:flex-row sm:items-start gap-4">
                <div class="flex-shrink-0">
                    <div class="h-11 w-11 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-white font-bold">
                        {{ strtoupper(substr($review->reviewer_name ?? 'A', 0, 1)) }}
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-3 mb-1">
                        <span class="font-semibold text-stone-800">{{ $review->reviewer_name }}</span>
                        <span class="text-stone-400 text-xs">{{ $review->created_at->diffForHumans() }}</span>
                        @if(!$review->is_approved)
                        <span class="text-xs px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded-full">Pending review</span>
                        @else
                        <span class="text-xs px-2 py-0.5 bg-green-100 text-green-700 rounded-full">Approved</span>
                        @endif
                    </div>
                    {{-- Star rating --}}
                    <div class="flex gap-1 mb-2">
                        @for($i = 1; $i <= 5; $i++)
                        <span class="text-{{ $i <= $review->rating ? 'amber' : 'slate' }}-{{ $i <= $review->rating ? '400' : '200' }}">★</span>
                        @endfor
                        <span class="text-xs text-stone-400 ml-1">({{ $review->rating }}/5)</span>
                    </div>
                    {{-- Sub-ratings --}}
                    @if($review->food_rating || $review->service_rating || $review->ambience_rating)
                    <div class="flex gap-4 text-xs text-stone-500 mb-2">
                        @if($review->food_rating)<span>🍽️ Food: {{ $review->food_rating }}/5</span>@endif
                        @if($review->service_rating)<span>👋 Service: {{ $review->service_rating }}/5</span>@endif
                        @if($review->ambience_rating)<span>✨ Ambience: {{ $review->ambience_rating }}/5</span>@endif
                    </div>
                    @endif
                    @if($review->menuItem)
                    <div class="text-xs text-amber-600 mb-2">Review for: {{ $review->menuItem->name }}</div>
                    @endif
                    <p class="text-sm text-stone-600">{{ $review->comment }}</p>
                </div>
                <div class="flex sm:flex-col gap-2 flex-shrink-0">
                    @if(!$review->is_approved)
                    <button wire:click="approve({{ $review->id }})" class="px-4 py-2 bg-green-600 hover:bg-green-500 text-white text-sm font-medium rounded-xl transition-colors">Approve</button>
                    @else
                    <button wire:click="reject({{ $review->id }})" class="px-4 py-2 bg-stone-200 hover:bg-stone-300 text-stone-700 text-sm font-medium rounded-xl transition-colors">Unapprove</button>
                    @endif
                    <button wire:click="delete({{ $review->id }})" wire:confirm="Delete this review?" class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 text-sm font-medium rounded-xl transition-colors">Delete</button>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-20 text-stone-400">
            <div class="text-5xl mb-3">⭐</div>
            <div>No reviews {{ $filter !== 'all' ? 'with status "' . $filter . '"' : 'yet' }}</div>
        </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $reviews->links() }}</div>
</div>
