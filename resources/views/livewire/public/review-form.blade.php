<div class="min-h-screen bg-gradient-to-br from-amber-50 to-orange-50 py-12 px-4">
    <div class="max-w-2xl mx-auto">
        <div class="text-center mb-8 animate-fade-in-up">
            <div class="text-5xl mb-3">⭐</div>
            <h1 class="text-3xl font-bold text-stone-800">Leave a Review</h1>
            <p class="text-stone-500 mt-2">Tell us about your dining experience</p>
        </div>

        @if(!$submitted)
        <div class="bg-white rounded-3xl shadow-sm border border-stone-100 p-8 space-y-6 animate-fade-in-up stagger-1">
            {{-- Order lookup --}}
            @if(!$order)
            <div>
                <label class="block text-sm font-semibold text-stone-700 mb-2">Order Number</label>
                <p class="text-xs text-stone-400 mb-3">Enter your completed order number (e.g. ORD-0001)</p>
                <div class="flex gap-3">
                    <input wire:model="orderNumber" wire:keydown.enter="lookupOrder" type="text" placeholder="ORD-XXXX"
                           class="flex-1 px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                    <button wire:click="lookupOrder"
                            class="px-6 py-3 bg-amber-600 hover:bg-amber-500 text-white rounded-xl font-semibold text-sm transition-colors">
                        Find Order
                    </button>
                </div>
                @if($error)
                <p class="mt-2 text-sm text-red-500">{{ $error }}</p>
                @endif
            </div>
            @else
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-800">
                ✅ <strong>Order #{{ $order->order_number }}</strong> verified — {{ $order->created_at->format('M d, Y') }}
            </div>
            @endif

            {{-- Reviewer name --}}
            <div>
                <label class="block text-sm font-semibold text-stone-700 mb-2">Your Name *</label>
                <input wire:model="reviewerName" type="text"
                       class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                @error('reviewerName') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Star ratings --}}
            <div class="space-y-4">
                <h3 class="font-semibold text-stone-800">Ratings</h3>

                @php
                $ratings = [
                    ['prop' => 'rating',         'label' => 'Overall Experience', 'icon' => '🌟'],
                    ['prop' => 'foodRating',      'label' => 'Food Quality',        'icon' => '🍽️'],
                    ['prop' => 'serviceRating',   'label' => 'Service',             'icon' => '👨‍🍳'],
                    ['prop' => 'ambienceRating',  'label' => 'Ambience',            'icon' => '✨'],
                ];
                @endphp

                @foreach($ratings as $r)
                <div class="flex items-center gap-4">
                    <div class="w-44 text-sm text-stone-600">{{ $r['icon'] }} {{ $r['label'] }}</div>
                    <div class="flex gap-1">
                        @for($i = 1; $i <= 5; $i++)
                        <button type="button" wire:click="$set('{{ $r['prop'] }}', {{ $i }})"
                                class="text-2xl transition-transform hover:scale-110 focus:outline-none
                                {{ ${$r['prop']} >= $i ? 'text-amber-400' : 'text-stone-200' }}">★</button>
                        @endfor
                    </div>
                    <span class="text-xs text-stone-400 w-6">{{ ${$r['prop']} }}/5</span>
                </div>
                @endforeach
                @error('rating') <p class="text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Comment --}}
            <div>
                <label class="block text-sm font-semibold text-stone-700 mb-2">Your Review *</label>
                <textarea wire:model="comment" rows="4" placeholder="Tell us about your experience (at least 10 characters)..."
                          class="w-full px-4 py-3 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm resize-none"></textarea>
                @error('comment') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <button wire:click="submitReview" wire:loading.attr="disabled"
                    class="w-full py-3 bg-amber-600 hover:bg-amber-500 disabled:opacity-50 text-white rounded-xl font-semibold transition-colors">
                <span wire:loading.remove>Submit Review</span>
                <span wire:loading>Submitting...</span>
            </button>
        </div>
        @else
        <div class="bg-white rounded-3xl shadow-sm border border-stone-100 p-12 text-center animate-pop-in">
            <div class="text-6xl mb-4">🙏</div>
            <h2 class="text-2xl font-bold text-stone-800 mb-2">Thank you!</h2>
            <p class="text-stone-500 mb-6">Your review has been submitted and is pending approval. We appreciate your feedback!</p>
            <a href="{{ route('menu') }}" class="inline-block px-8 py-3 bg-amber-600 hover:bg-amber-500 text-white rounded-xl font-semibold transition-colors">
                Back to Menu
            </a>
        </div>
        @endif
    </div>
</div>
