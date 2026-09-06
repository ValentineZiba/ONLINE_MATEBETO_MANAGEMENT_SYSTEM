<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }} | Matebeto Restaurant</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-display { font-family: 'Playfair Display', serif; }
        .hero-gradient { background: linear-gradient(135deg, #1C1917 0%, #292524 50%, #1C1917 100%); }
        .gold-gradient { background: linear-gradient(135deg, #D97706, #B45309); }
    </style>
</head>
<body class="bg-stone-50 text-stone-800 antialiased">

{{-- Navigation --}}
<nav x-data="{ open: false, cartOpen: false }" class="fixed top-0 left-0 right-0 z-50 bg-stone-900/95 backdrop-blur-sm border-b border-amber-700/30">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-full gold-gradient flex items-center justify-center text-white font-bold text-lg shadow-lg group-hover:shadow-amber-500/30 transition-shadow">M</div>
                <div>
                    <div class="font-display text-lg text-white font-semibold leading-none">Matebeto</div>
                    <div class="text-amber-400 text-xs tracking-widest uppercase">Restaurant</div>
                </div>
            </a>

            {{-- Desktop Nav --}}
            <div class="hidden md:flex items-center gap-8">
                <a href="{{ route('home') }}" class="text-stone-300 hover:text-amber-400 text-sm font-medium transition-colors {{ request()->routeIs('home') ? 'text-amber-400' : '' }}">Home</a>
                <a href="{{ route('menu') }}" class="text-stone-300 hover:text-amber-400 text-sm font-medium transition-colors {{ request()->routeIs('menu') ? 'text-amber-400' : '' }}">Menu</a>
                <a href="{{ route('reservations') }}" class="text-stone-300 hover:text-amber-400 text-sm font-medium transition-colors {{ request()->routeIs('reservations') ? 'text-amber-400' : '' }}">Reservations</a>
                <a href="{{ route('order.track') }}" class="text-stone-300 hover:text-amber-400 text-sm font-medium transition-colors">Track Order</a>
                @if(auth()->check() && auth()->user()->isStaff())
                    <a href="{{ route('admin.dashboard') }}" class="text-amber-400 hover:text-amber-300 text-sm font-medium transition-colors">Admin Panel</a>
                @endif
            </div>

            {{-- Right Side Actions --}}
            <div class="flex items-center gap-4">
                {{-- Cart Button --}}
                @php $cartCount = count(session('cart', [])); @endphp
                <button @click="cartOpen = !cartOpen" class="relative p-2 text-stone-300 hover:text-amber-400 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    @if($cartCount > 0)
                    <span id="cart-count" class="absolute -top-1 -right-1 bg-amber-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold">{{ $cartCount }}</span>
                    @endif
                </button>

                @auth
                    <div x-data="{ userMenu: false }" class="relative">
                        <button @click="userMenu = !userMenu" class="flex items-center gap-2 text-stone-300 hover:text-amber-400 transition-colors">
                            <div class="w-8 h-8 rounded-full bg-amber-600 flex items-center justify-center text-white text-sm font-semibold">{{ auth()->user()->initials() }}</div>
                        </button>
                        <div x-show="userMenu" @click.away="userMenu = false" class="absolute right-0 mt-2 w-48 bg-stone-800 border border-stone-700 rounded-xl shadow-2xl py-2 z-50">
                            <div class="px-4 py-2 border-b border-stone-700">
                                <div class="text-white text-sm font-medium truncate">{{ auth()->user()->name }}</div>
                                <div class="text-stone-400 text-xs truncate">{{ auth()->user()->email }}</div>
                            </div>
                            @if(auth()->user()->isStaff())
                                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-stone-300 hover:bg-stone-700 hover:text-white transition-colors">Admin Panel</a>
                            @else
                                <a href="{{ route('customer.orders') }}" class="block px-4 py-2 text-sm text-stone-300 hover:bg-stone-700 hover:text-white transition-colors">My Orders</a>
                                <a href="{{ route('customer.reservations') }}" class="block px-4 py-2 text-sm text-stone-300 hover:bg-stone-700 hover:text-white transition-colors">My Reservations</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}" class="border-t border-stone-700 mt-1 pt-1">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-stone-700 hover:text-red-300 transition-colors">Sign Out</button>
                            </form>
                        </div>
                    </div>
                @endauth

                {{-- Auth links: hidden on mobile (shown in mobile menu) --}}
                @auth
                {{-- user dropdown already above --}}
                @else
                    <a href="{{ route('login') }}" class="hidden md:block text-stone-300 hover:text-amber-400 text-sm font-medium transition-colors">Sign In</a>
                    <a href="{{ route('register') }}" class="hidden md:block px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white text-sm font-medium rounded-lg transition-colors whitespace-nowrap">Join Us</a>
                @endauth

                {{-- Mobile menu button --}}
                <button @click="open = !open" class="md:hidden p-2 text-stone-300 hover:text-amber-400 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile Nav --}}
        <div x-show="open" x-transition class="md:hidden py-4 border-t border-stone-700 space-y-1">
            <a href="{{ route('home') }}" class="block px-4 py-3 text-stone-300 hover:text-amber-400 hover:bg-stone-800 rounded-lg mx-2">Home</a>
            <a href="{{ route('menu') }}" class="block px-4 py-3 text-stone-300 hover:text-amber-400 hover:bg-stone-800 rounded-lg mx-2">Menu</a>
            <a href="{{ route('reservations') }}" class="block px-4 py-3 text-stone-300 hover:text-amber-400 hover:bg-stone-800 rounded-lg mx-2">Reservations</a>
            <a href="{{ route('order.track') }}" class="block px-4 py-3 text-stone-300 hover:text-amber-400 hover:bg-stone-800 rounded-lg mx-2">Track Order</a>
            @if(auth()->check() && auth()->user()->isStaff())
                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-3 text-amber-400 hover:bg-stone-800 rounded-lg mx-2">Admin Panel</a>
            @endif
            @guest
            <div class="pt-2 px-2 flex gap-2 border-t border-stone-700 mt-2">
                <a href="{{ route('login') }}" class="flex-1 text-center py-2.5 border border-stone-600 text-stone-300 rounded-xl text-sm font-medium">Sign In</a>
                <a href="{{ route('register') }}" class="flex-1 text-center py-2.5 bg-amber-600 text-white rounded-xl text-sm font-medium">Join Us</a>
            </div>
            @endguest
        </div>
    </div>

    {{-- Cart Drawer --}}
    <div x-show="cartOpen" @click.away="cartOpen = false"
         x-transition:enter="transform transition ease-in-out duration-300"
         x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
         x-transition:leave="transform transition ease-in-out duration-300"
         x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
         class="fixed top-0 right-0 h-full w-full sm:w-96 bg-stone-900 border-l border-stone-700 shadow-2xl z-50 flex flex-col">
        <div class="flex items-center justify-between p-6 border-b border-stone-700">
            <h2 class="font-display text-xl text-white">Your Cart</h2>
            <button @click="cartOpen = false" class="text-stone-400 hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        @php
            $cart = session('cart', []);
            $cartTotal = collect($cart)->sum('subtotal');
        @endphp
        <div class="flex-1 overflow-y-auto p-6 space-y-4">
            @forelse($cart as $key => $item)
            <div class="flex items-center gap-4 bg-stone-800 rounded-xl p-3">
                <div class="flex-1">
                    <div class="text-white text-sm font-medium">{{ $item['name'] }}</div>
                    <div class="text-amber-400 text-xs">K {{ number_format($item['price'], 2) }} × {{ $item['quantity'] }}</div>
                </div>
                <div class="text-white font-semibold text-sm">K {{ number_format($item['subtotal'], 2) }}</div>
            </div>
            @empty
            <div class="text-center text-stone-400 py-12">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 text-stone-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <p class="text-lg font-medium text-stone-500">Your cart is empty</p>
                <p class="text-sm mt-1">Browse our menu to add items</p>
            </div>
            @endforelse
        </div>
        @if(count($cart) > 0)
        <div class="p-6 border-t border-stone-700 space-y-4">
            <div class="flex items-center justify-between text-stone-300">
                <span>Subtotal</span><span class="font-semibold text-white">K {{ number_format($cartTotal, 2) }}</span>
            </div>
            <div class="flex items-center justify-between text-stone-300">
                <span>Tax (16%)</span><span>K {{ number_format($cartTotal * 0.16, 2) }}</span>
            </div>
            <div class="flex items-center justify-between text-lg font-bold text-white border-t border-stone-700 pt-3">
                <span>Total</span><span class="text-amber-400">K {{ number_format($cartTotal * 1.16, 2) }}</span>
            </div>
            <a href="{{ route('menu') }}" class="block w-full py-3 bg-amber-600 hover:bg-amber-500 text-white text-center font-semibold rounded-xl transition-colors">
                Proceed to Checkout
            </a>
        </div>
        @endif
    </div>
</nav>

{{-- Main Content --}}
<main class="pt-16">
    {{ $slot }}
</main>

{{-- Footer --}}
<footer class="bg-stone-900 text-stone-400 mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full gold-gradient flex items-center justify-center text-white font-bold text-lg">M</div>
                    <div>
                        <div class="font-display text-xl text-white font-semibold">Matebeto Restaurant</div>
                        <div class="text-amber-400 text-xs tracking-widest uppercase">Where Every Meal is a Memory</div>
                    </div>
                </div>
                <p class="text-stone-400 text-sm leading-relaxed max-w-sm">Experience the finest Zambian and International cuisine in a warm, welcoming atmosphere. We are dedicated to providing an exceptional dining experience.</p>
                <div class="flex gap-4 mt-6">
                    <a href="#" class="w-9 h-9 bg-stone-800 hover:bg-amber-600 rounded-full flex items-center justify-center text-stone-400 hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="#" class="w-9 h-9 bg-stone-800 hover:bg-amber-600 rounded-full flex items-center justify-center text-stone-400 hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                    <a href="#" class="w-9 h-9 bg-stone-800 hover:bg-amber-600 rounded-full flex items-center justify-center text-stone-400 hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/></svg>
                    </a>
                </div>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-4">Quick Links</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('menu') }}" class="hover:text-amber-400 transition-colors">Our Menu</a></li>
                    <li><a href="{{ route('reservations') }}" class="hover:text-amber-400 transition-colors">Make a Reservation</a></li>
                    <li><a href="{{ route('order.track') }}" class="hover:text-amber-400 transition-colors">Track Your Order</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-amber-400 transition-colors">Sign In</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-4">Contact Us</h4>
                <ul class="space-y-3 text-sm">
                    <li class="flex gap-3"><span class="text-amber-500">📍</span><span>123 Cairo Road, Lusaka, Zambia</span></li>
                    <li class="flex gap-3"><span class="text-amber-500">📞</span><span>+260 977 123 456</span></li>
                    <li class="flex gap-3"><span class="text-amber-500">✉️</span><span>info@matebeto.com</span></li>
                    <li class="flex gap-3"><span class="text-amber-500">🕐</span><span>Mon–Sun: 7AM – 11PM</span></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-stone-800 mt-12 pt-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <p class="text-sm">© {{ date('Y') }} Matebeto Restaurant. All rights reserved.</p>
            <p class="text-sm text-stone-500">Crafted with ❤️ in Lusaka, Zambia</p>
        </div>
    </div>
</footer>

@livewireScripts
</body>
</html>
