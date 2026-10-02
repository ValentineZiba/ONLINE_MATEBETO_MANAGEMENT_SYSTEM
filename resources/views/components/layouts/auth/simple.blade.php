<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        <style>
            .sunburst {
                background-color: #92400e;
                background-image: conic-gradient(
                    from 0deg,
                    #92400e 0deg, #fef3c7 10deg,
                    #92400e 20deg, #fef3c7 30deg,
                    #92400e 40deg, #fef3c7 50deg,
                    #92400e 60deg, #fef3c7 70deg,
                    #92400e 80deg, #fef3c7 90deg,
                    #92400e 100deg, #fef3c7 110deg,
                    #92400e 120deg, #fef3c7 130deg,
                    #92400e 140deg, #fef3c7 150deg,
                    #92400e 160deg, #fef3c7 170deg,
                    #92400e 180deg, #fef3c7 190deg,
                    #92400e 200deg, #fef3c7 210deg,
                    #92400e 220deg, #fef3c7 230deg,
                    #92400e 240deg, #fef3c7 250deg,
                    #92400e 260deg, #fef3c7 270deg,
                    #92400e 280deg, #fef3c7 290deg,
                    #92400e 300deg, #fef3c7 310deg,
                    #92400e 320deg, #fef3c7 330deg,
                    #92400e 340deg, #fef3c7 350deg,
                    #92400e 360deg
                );
            }
        </style>
    </head>
    <body class="min-h-screen antialiased">

        {{-- Full-screen sunburst background --}}
        <div class="sunburst min-h-screen flex items-center justify-center p-4 relative">

            {{-- Radial overlay to soften centre --}}
            <div class="absolute inset-0 pointer-events-none" style="background: radial-gradient(ellipse at center, rgba(120,53,15,0.35) 0%, transparent 70%)"></div>

            {{-- Login card --}}
            <div class="relative z-10 w-full max-w-sm animate-pop-in">

                {{-- Card --}}
                <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">

                    {{-- Card header with restaurant branding --}}
                    <div class="bg-amber-800 px-8 pt-8 pb-6 text-center">
                        <a href="{{ route('home') }}" class="inline-flex flex-col items-center gap-2" wire:navigate>
                            <div class="w-16 h-16 bg-white/15 rounded-2xl flex items-center justify-center mb-1">
                                <span class="text-3xl">🍽️</span>
                            </div>
                            <span class="text-white font-bold text-xl tracking-wide">{{ config('app.name', 'Matebeto') }}</span>
                            <span class="text-amber-200 text-xs tracking-widest uppercase">Restaurant</span>
                        </a>
                    </div>

                    {{-- Divider accent --}}
                    <div class="h-1 bg-gradient-to-r from-amber-400 via-amber-500 to-amber-600"></div>

                    {{-- Form area --}}
                    <div class="px-8 py-7">
                        <div class="flex flex-col gap-6">
                            {{ $slot }}
                        </div>
                    </div>

                </div>

                {{-- Back to site link --}}
                <p class="text-center mt-5 text-amber-100/70 text-xs">
                    <a href="{{ route('home') }}" wire:navigate class="hover:text-amber-100 transition-colors underline underline-offset-2">
                        ← Back to restaurant
                    </a>
                </p>

            </div>
        </div>

        @fluxScripts
    </body>
</html>
