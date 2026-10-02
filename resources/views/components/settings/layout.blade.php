<div class="flex flex-col lg:flex-row gap-6 lg:gap-8 items-start">
    <nav class="w-full lg:w-56 flex-shrink-0 flex lg:flex-col gap-2 overflow-x-auto lg:overflow-visible pb-1 lg:pb-0 scrollbar-hide">
        <a href="{{ route('settings.profile') }}" wire:navigate
           class="flex-shrink-0 px-4 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('settings.profile') ? 'bg-amber-600 text-white shadow-sm' : 'text-stone-600 hover:bg-stone-100' }}">
            Profile
        </a>
        <a href="{{ route('settings.password') }}" wire:navigate
           class="flex-shrink-0 px-4 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('settings.password') ? 'bg-amber-600 text-white shadow-sm' : 'text-stone-600 hover:bg-stone-100' }}">
            Password
        </a>
        <a href="{{ route('settings.appearance') }}" wire:navigate
           class="flex-shrink-0 px-4 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('settings.appearance') ? 'bg-amber-600 text-white shadow-sm' : 'text-stone-600 hover:bg-stone-100' }}">
            Appearance
        </a>
    </nav>

    <div class="flex-1 w-full bg-white rounded-3xl shadow-sm border border-stone-100 p-6 sm:p-8">
        <h2 class="font-display text-xl font-bold text-stone-800">{{ $heading ?? '' }}</h2>
        @if($subheading ?? false)
        <p class="text-stone-500 text-sm mt-1">{{ $subheading }}</p>
        @endif

        <div class="mt-5 w-full max-w-lg">
            {{ $slot }}
        </div>
    </div>
</div>
