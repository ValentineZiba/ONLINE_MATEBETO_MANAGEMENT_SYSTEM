<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Bar' }} | Matebeto</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        body { font-family: 'Inter', sans-serif; background: #1c1917; }
        .font-display { font-family: 'Playfair Display', serif; }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #78716c; border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: #d97706; }
    </style>
</head>
<body class="text-stone-100 min-h-screen" style="background: linear-gradient(160deg,#1c1917 0%,#1a1007 60%,#1c1917 100%);">

    {{-- Amber accent top line --}}
    <div class="h-[3px] bg-gradient-to-r from-amber-700 via-amber-400 to-amber-700"></div>

    <header class="px-6 py-3.5 flex items-center justify-between border-b border-stone-800/70 backdrop-blur-sm"
            style="background: rgba(28,25,23,0.92);">
        {{-- Brand --}}
        <div class="flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-amber-400 to-amber-700 flex items-center justify-center text-xl shadow-lg shadow-amber-900/50 flex-shrink-0">🍸</div>
            <div>
                <div class="font-display text-base font-semibold text-white leading-tight">{{ $title ?? 'Bar' }}</div>
                <div class="text-stone-500 text-xs tracking-wide">Matebeto — Bar Station</div>
            </div>
        </div>

        {{-- Nav --}}
        <div class="flex items-center gap-2">
            @php $isDisplay = request()->routeIs('bar.display'); $isTabs = request()->routeIs('bar.tabs'); @endphp
            <a href="{{ route('bar.display') }}"
               class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-sm font-medium transition-all
                      {{ $isDisplay ? 'bg-amber-600 text-white shadow-lg shadow-amber-900/30' : 'text-stone-400 hover:text-stone-100 hover:bg-stone-800' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Orders
            </a>
            <a href="{{ route('bar.tabs') }}"
               class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-sm font-medium transition-all
                      {{ $isTabs ? 'bg-amber-600 text-white shadow-lg shadow-amber-900/30' : 'text-stone-400 hover:text-stone-100 hover:bg-stone-800' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                Tabs
            </a>
            <div class="w-px h-5 bg-stone-700 mx-1"></div>
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-stone-800/60">
                <div class="h-1.5 w-1.5 rounded-full bg-amber-400 animate-pulse"></div>
                <div id="clock" class="text-white font-bold text-sm tabular-nums">--:--</div>
            </div>
            <a href="{{ route('admin.dashboard') }}"
               class="px-3.5 py-2 rounded-xl text-sm text-stone-500 hover:text-stone-300 hover:bg-stone-800 transition-all">
                ← Admin
            </a>
        </div>
    </header>

    <main class="p-5">
        {{ $slot }}
    </main>

    @livewireScripts
    <script>
        function updateClock() {
            const now = new Date();
            document.getElementById('clock').textContent =
                now.toLocaleTimeString('en-ZM', { hour: '2-digit', minute: '2-digit' });
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>
</html>
