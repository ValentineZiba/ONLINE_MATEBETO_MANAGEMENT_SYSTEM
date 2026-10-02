<a
    {{ $attributes->merge(['class' => 'underline text-sm decoration-amber-400 underline-offset-2 duration-300 ease-out hover:decoration-amber-600 text-amber-700 font-semibold dark:text-amber-400 dark:hover:decoration-amber-300']) }}
    wire:navigate
>
    {{ $slot }}
</a>
