<div>
    {{-- Hero Section --}}
    <section class="hero-gradient min-h-screen flex items-center justify-center relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=1920&q=80')] bg-cover bg-center opacity-20"></div>
        <div class="relative max-w-4xl mx-auto px-6 text-center">
            <div class="inline-block px-4 py-2 bg-amber-600/20 border border-amber-500/30 rounded-full text-amber-400 text-sm font-medium mb-6 tracking-wider uppercase animate-fade-in-up">
                Welcome to Matebeto Restaurant
            </div>
            <h1 class="font-display text-5xl md:text-7xl text-white font-bold mb-6 leading-tight animate-fade-in-up stagger-1">
                Where Every Meal<br><span class="text-amber-400">is a Memory</span>
            </h1>
            <p class="text-stone-300 text-lg md:text-xl max-w-2xl mx-auto mb-10 leading-relaxed animate-fade-in-up stagger-2">
                Experience the finest Zambian and International cuisine in Lusaka. Fresh ingredients, authentic flavors, unforgettable experiences.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center animate-fade-in-up stagger-3">
                <a href="{{ route('menu') }}" class="px-8 py-4 bg-amber-600 hover:bg-amber-500 text-white font-semibold rounded-xl transition-all shadow-lg shadow-amber-900/30 hover:shadow-amber-900/50 hover:-translate-y-0.5">
                    Order Now
                </a>
                <a href="{{ route('reservations') }}" class="px-8 py-4 border border-stone-500 hover:border-amber-500 text-white hover:text-amber-400 font-semibold rounded-xl transition-all">
                    Book a Table
                </a>
            </div>
            <div class="flex items-center justify-center gap-8 mt-12 text-stone-400 animate-fade-in-up stagger-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-white">15+</div>
                    <div class="text-xs uppercase tracking-wider">Years of Excellence</div>
                </div>
                <div class="w-px h-10 bg-stone-700"></div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-white">50+</div>
                    <div class="text-xs uppercase tracking-wider">Menu Items</div>
                </div>
                <div class="w-px h-10 bg-stone-700"></div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-white">4.9★</div>
                    <div class="text-xs uppercase tracking-wider">Avg Rating</div>
                </div>
            </div>
        </div>
        {{-- Scroll indicator --}}
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 text-stone-400 animate-bounce">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </section>

    {{-- Category Showcase --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 reveal-up">
                <h2 class="font-display text-4xl font-bold text-stone-800 mb-4">Explore Our Menu</h2>
                <p class="text-stone-500 max-w-lg mx-auto">From hearty breakfasts to lavish dinners, we have something for every palate.</p>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-5 gap-4">
                @foreach($categories as $cat)
                <a href="{{ route('menu', ['category' => $cat->slug]) }}"
                   class="group text-center p-4 rounded-2xl bg-stone-50 hover:bg-amber-50 border border-stone-100 hover:border-amber-200 transition-all hover:-translate-y-1 cursor-pointer reveal-up stagger-{{ ($loop->iteration - 1) % 6 + 1 }}">
                    <div class="text-4xl mb-3">{{ $cat->icon }}</div>
                    <div class="text-sm font-semibold text-stone-700 group-hover:text-amber-700">{{ $cat->name }}</div>
                </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Featured Dishes --}}
    <section class="py-20 bg-stone-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between mb-12 reveal-up">
                <div>
                    <div class="text-amber-600 text-sm font-semibold tracking-wider uppercase mb-2">Chef's Picks</div>
                    <h2 class="font-display text-4xl font-bold text-stone-800">Featured Dishes</h2>
                </div>
                <a href="{{ route('menu') }}" class="text-amber-600 hover:text-amber-700 font-medium text-sm flex items-center gap-1 group">
                    View Full Menu
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($featuredItems as $item)
                <div class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 reveal-up stagger-{{ ($loop->iteration - 1) % 6 + 1 }}">
                    <div class="relative h-52 overflow-hidden">
                        @if($item->image)
                            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-amber-50 via-orange-50 to-stone-100 flex flex-col items-center justify-center relative overflow-hidden">
                                <div class="absolute top-0 right-0 w-28 h-28 bg-amber-100 rounded-full -translate-y-10 translate-x-10 opacity-60"></div>
                                <div class="absolute bottom-0 left-0 w-24 h-24 bg-orange-100 rounded-full translate-y-10 -translate-x-10 opacity-60"></div>
                                <div class="relative w-16 h-16 bg-white rounded-2xl shadow flex items-center justify-center mb-2 border border-amber-100">
                                    <svg class="w-8 h-8 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <span class="relative text-xs font-semibold text-amber-600/80 uppercase tracking-widest">{{ $item->category->name }}</span>
                            </div>
                        @endif
                        <div class="absolute top-3 left-3 flex gap-2 flex-wrap">
                            @if($item->is_vegetarian)
                                <span class="px-2 py-1 bg-green-500 text-white text-xs rounded-full font-medium">🌿 Veg</span>
                            @endif
                            @if($item->is_spicy)
                                <span class="px-2 py-1 bg-red-500 text-white text-xs rounded-full font-medium">🌶 Spicy</span>
                            @endif
                            @if($item->discount_price)
                                <span class="px-2 py-1 bg-amber-500 text-white text-xs rounded-full font-medium">Sale</span>
                            @endif
                        </div>
                        <div class="absolute top-3 right-3 px-3 py-1 bg-stone-900/80 backdrop-blur-sm text-white text-sm font-bold rounded-full">
                            K {{ number_format($item->effective_price, 0) }}
                        </div>
                    </div>
                    <div class="p-5">
                        <div class="text-amber-600 text-xs font-medium mb-1 uppercase tracking-wide">{{ $item->category->name }}</div>
                        <h3 class="font-semibold text-stone-800 text-lg mb-2 leading-tight">{{ $item->name }}</h3>
                        <p class="text-stone-500 text-sm leading-relaxed mb-4 line-clamp-2">{{ $item->description }}</p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3 text-xs text-stone-400">
                                @if($item->preparation_time)
                                <span class="flex items-center gap-1">⏱ {{ $item->preparation_time }}min</span>
                                @endif
                                @if($item->calories)
                                <span class="flex items-center gap-1">🔥 {{ $item->calories }}cal</span>
                                @endif
                            </div>
                            <a href="{{ route('menu') }}"
                               class="px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white text-sm font-semibold rounded-xl transition-colors">
                                Order
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Why Choose Us --}}
    <section class="py-20 hero-gradient">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 reveal-up">
                <h2 class="font-display text-4xl font-bold text-white mb-4">Why Matebeto?</h2>
                <p class="text-stone-400 max-w-lg mx-auto">We go beyond just serving food — we create experiences.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach([
                    ['icon' => '🥘', 'title' => 'Fresh Local Ingredients', 'desc' => 'We source directly from Zambian farmers and markets, ensuring every dish is bursting with fresh, authentic flavors.'],
                    ['icon' => '👨‍🍳', 'title' => 'Expert Chefs', 'desc' => 'Our culinary team brings decades of experience in Zambian and International cuisine, crafting every plate with precision and passion.'],
                    ['icon' => '🚀', 'title' => 'Fast & Reliable', 'desc' => 'From kitchen to table in under 30 minutes. Our efficient team ensures your food arrives hot, fresh, and exactly as ordered.'],
                    ['icon' => '📱', 'title' => 'Easy Online Ordering', 'desc' => 'Order from your phone, track in real-time, and pay digitally. Restaurant convenience reimagined for the modern diner.'],
                    ['icon' => '🎉', 'title' => 'Private Events', 'desc' => 'Birthdays, anniversaries, corporate events — our private dining rooms accommodate up to 20 guests with personalized menus.'],
                    ['icon' => '♻️', 'title' => 'Sustainability First', 'desc' => 'Eco-friendly packaging, zero food waste initiatives, and local sourcing — we are committed to a greener tomorrow.'],
                ] as $feature)
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 hover:bg-white/10 transition-colors reveal-up stagger-{{ ($loop->iteration - 1) % 6 + 1 }}">
                    <div class="text-4xl mb-4">{{ $feature['icon'] }}</div>
                    <h3 class="text-white font-semibold text-lg mb-2">{{ $feature['title'] }}</h3>
                    <p class="text-stone-400 text-sm leading-relaxed">{{ $feature['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Testimonials --}}
    @if($reviews->isNotEmpty())
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 reveal-up">
                <div class="text-amber-600 text-sm font-semibold tracking-wider uppercase mb-2">Testimonials</div>
                <h2 class="font-display text-4xl font-bold text-stone-800">What Our Guests Say</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($reviews->take(3) as $review)
                <div class="bg-stone-50 rounded-2xl p-6 border border-stone-100 reveal-up stagger-{{ ($loop->iteration - 1) % 6 + 1 }}">
                    <div class="flex text-amber-400 mb-4">
                        @for($i = 1; $i <= 5; $i++)
                            <span>{{ $i <= $review->rating ? '★' : '☆' }}</span>
                        @endfor
                    </div>
                    <p class="text-stone-600 text-sm leading-relaxed mb-4 italic">"{{ $review->comment }}"</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-amber-600 flex items-center justify-center text-white font-semibold text-sm">
                            {{ strtoupper(substr($review->reviewer_name ?? $review->user?->name ?? 'G', 0, 1)) }}
                        </div>
                        <div>
                            <div class="font-semibold text-stone-800 text-sm">{{ $review->reviewer_name ?? $review->user?->name ?? 'Guest' }}</div>
                            <div class="text-stone-400 text-xs">{{ $review->created_at->format('M Y') }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- CTA Section --}}
    <section class="py-20 bg-amber-600">
        <div class="max-w-4xl mx-auto px-4 text-center reveal-up">
            <h2 class="font-display text-4xl font-bold text-white mb-4">Ready to Experience Matebeto?</h2>
            <p class="text-amber-100 text-lg mb-8">Join thousands of happy diners who've made Matebeto their favourite restaurant in Lusaka.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('menu') }}" class="px-8 py-4 bg-white text-amber-700 hover:bg-amber-50 font-bold rounded-xl transition-colors shadow-lg">
                    Order Online Now
                </a>
                <a href="{{ route('reservations') }}" class="px-8 py-4 border-2 border-white text-white hover:bg-white/10 font-bold rounded-xl transition-colors">
                    Reserve a Table
                </a>
            </div>
        </div>
    </section>
</div>
