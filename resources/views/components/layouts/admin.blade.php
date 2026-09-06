<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin' }} | Matebeto Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-display { font-family: 'Playfair Display', serif; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #1c1917; }
        ::-webkit-scrollbar-thumb { background: #d97706; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #f59e0b; }
    </style>
</head>
<body class="bg-stone-50 antialiased" x-data="{ sidebarOpen: true, mobileOpen: false }">

<div class="flex h-screen overflow-hidden">
    {{-- Mobile sidebar overlay backdrop --}}
    <div x-show="mobileOpen"
         x-transition:enter="transition-opacity ease-in duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-out duration-200"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click="mobileOpen = false"
         class="fixed inset-0 bg-black/60 z-30 md:hidden"></div>

    {{-- Sidebar --}}
    <aside :class="[
              sidebarOpen ? 'md:w-64' : 'md:w-16',
              mobileOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'
           ]"
           class="bg-stone-900 flex flex-col flex-shrink-0 transition-all duration-300 overflow-hidden
                  fixed md:relative z-40 h-full w-72 md:flex">
        {{-- Amber top accent --}}
        <div class="h-1 bg-gradient-to-r from-amber-500 via-amber-400 to-amber-600 flex-shrink-0"></div>
        {{-- Logo --}}
        <div class="flex items-center h-16 px-4 border-b border-stone-700/50 flex-shrink-0">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-500 to-amber-700 flex items-center justify-center text-white font-bold text-lg flex-shrink-0 shadow-lg shadow-amber-900/40">M</div>
            <div x-show="sidebarOpen" class="ml-3 overflow-hidden">
                <div class="text-white font-semibold text-sm leading-none font-display">Matebeto</div>
                <div class="text-amber-400/80 text-xs mt-0.5 tracking-wide">Admin Panel</div>
            </div>
            <button @click="sidebarOpen = !sidebarOpen" class="ml-auto text-stone-500 hover:text-amber-400 transition-colors flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 py-6 overflow-y-auto overflow-x-hidden">
            @php
                $navGroups = [
                    'Overview' => [
                        ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                        ['route' => 'admin.analytics', 'label' => 'Analytics', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                    ],
                    'Operations' => [
                        ['route' => 'admin.orders', 'label' => 'Orders', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                        ['route' => 'admin.payments', 'label' => 'Payments', 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                        ['route' => 'admin.tables', 'label' => 'Tables', 'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
                        ['route' => 'admin.reservations', 'label' => 'Reservations', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                        ['route' => 'admin.waitlist', 'label' => 'Waitlist', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                        ['route' => 'admin.delivery-riders', 'label' => 'Delivery Riders', 'icon' => 'M8 17a2 2 0 100 4 2 2 0 000-4zm0 0h8m0 0a2 2 0 100 4 2 2 0 000-4zm0 0V7a1 1 0 00-1-1H4a1 1 0 00-1 1v10h1m14-4h3l-3-5h-4'],
                        ['route' => 'kitchen.display', 'label' => 'Kitchen Display', 'icon' => 'M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z'],
                    ],
                    'Menu' => [
                        ['route' => 'admin.menu.categories', 'label' => 'Categories', 'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'],
                        ['route' => 'admin.menu.items', 'label' => 'Menu Items', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                        ['route' => 'admin.coupons', 'label' => 'Coupons', 'icon' => 'M7 7h.01M3 6l3 3m0 0l.5 3M6 9l3 3m4-10l-7 7m6.5 6.5l3.5-3.5M15 6l3 3-3 3-3-3 3-3z'],
                    ],
                    'Customers' => [
                        ['route' => 'admin.reviews', 'label' => 'Reviews', 'icon' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z'],
                        ['route' => 'admin.users', 'label' => 'Users', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'],
                    ],
                    'Bar' => [
                        ['route' => 'bar.display', 'label' => 'Bar Display', 'icon' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                        ['route' => 'bar.tabs',    'label' => 'Bar Tabs',    'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                    ],
                    'Staff & Stock' => [
                        ['route' => 'admin.scheduling', 'label' => 'Scheduling', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                        ['route' => 'admin.inventory', 'label' => 'Inventory', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                    ],
                    'System' => [
                        ['route' => 'admin.settings', 'label' => 'Settings', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
                        ['route' => 'admin.activity-log', 'label' => 'Activity Log', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ],
                ];
            @endphp

            @foreach($navGroups as $groupName => $items)
            <div class="mb-6">
                <div x-show="sidebarOpen" class="px-4 mb-2">
                    <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">{{ $groupName }}</span>
                </div>
                @foreach($items as $item)
                <a href="{{ route($item['route']) }}"
                   class="relative flex items-center px-4 py-2.5 text-sm transition-all duration-150 group {{ request()->routeIs($item['route']) ? 'bg-amber-500/10 text-amber-400' : 'text-stone-400 hover:bg-stone-800/60 hover:text-stone-100' }}">
                    @if(request()->routeIs($item['route']))
                    <span class="absolute left-0 top-1 bottom-1 w-0.5 bg-amber-500 rounded-full"></span>
                    @endif
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0 {{ request()->routeIs($item['route']) ? 'text-amber-400' : 'text-stone-500 group-hover:text-stone-300' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $item['icon'] }}"/>
                    </svg>
                    <span x-show="sidebarOpen" class="ml-3 truncate font-medium">{{ $item['label'] }}</span>
                </a>
                @endforeach
            </div>
            @endforeach
        </nav>

        {{-- Sidebar footer --}}
        <div class="p-4 border-t border-stone-700/50 flex-shrink-0 space-y-3">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-amber-600 flex items-center justify-center text-white text-sm font-semibold flex-shrink-0">{{ auth()->user()->initials() }}</div>
                <div x-show="sidebarOpen" class="overflow-hidden flex-1 min-w-0">
                    <div class="text-white text-sm font-medium truncate">{{ auth()->user()->name }}</div>
                    <div class="text-stone-400 text-xs truncate">{{ ucfirst(auth()->user()->role) }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" x-show="sidebarOpen">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-2 px-3 py-2 rounded-xl text-stone-400 hover:bg-red-900/30 hover:text-red-400 transition-colors text-sm font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Sign Out
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Content --}}
    <div class="flex-1 flex flex-col min-h-0 overflow-hidden">
        {{-- Top Bar --}}
        <header class="bg-white border-b border-stone-200 flex items-center justify-between px-6 h-16 flex-shrink-0 shadow-sm">
            <div class="flex items-center gap-4">
                <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 text-stone-400 hover:text-stone-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div>
                    <h1 class="text-lg font-semibold text-stone-800">{{ $title ?? 'Dashboard' }}</h1>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('home') }}" target="_blank" class="text-sm text-stone-500 hover:text-amber-600 transition-colors flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    View Site
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-stone-500 hover:text-red-500 transition-colors">Sign Out</button>
                </form>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto bg-stone-50 p-3 sm:p-6">
            {{ $slot }}
        </main>
    </div>
</div>

@livewireScripts
</body>
</html>
