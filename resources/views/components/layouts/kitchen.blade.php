<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kitchen Display | Matebeto</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        body { font-family: 'Inter', sans-serif; background: #0f172a; }
    </style>
</head>
<body class="bg-slate-900 text-white min-h-screen">
    <header class="bg-slate-800 border-b border-slate-700 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-amber-700 flex items-center justify-center text-white font-bold text-lg">M</div>
            <div>
                <h1 class="text-xl font-bold text-white">Kitchen Display System</h1>
                <p class="text-slate-400 text-sm">Matebeto Restaurant</p>
            </div>
        </div>
        <div class="flex items-center gap-6">
            <div class="text-right">
                <div class="text-slate-400 text-xs">Current Time</div>
                <div id="clock" class="text-white font-bold text-lg">--:--</div>
            </div>
            <a href="{{ route('admin.orders') }}" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white text-sm rounded-lg transition-colors">Orders</a>
            <a href="{{ route('home') }}" class="px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white text-sm rounded-lg transition-colors">Home</a>
        </div>
    </header>

    <main class="p-6">
        {{ $slot }}
    </main>

    @livewireScripts
    <script>
        function updateClock() {
            const now = new Date();
            document.getElementById('clock').textContent = now.toLocaleTimeString('en-KE', { hour: '2-digit', minute: '2-digit' });
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>
</html>
