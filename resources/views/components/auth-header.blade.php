@props([
    'title',
    'description',
])

<div class="flex w-full flex-col gap-1 text-center">
    <h1 class="text-lg font-semibold text-stone-800">{{ $title }}</h1>
    <p class="text-center text-sm text-stone-500">{{ $description }}</p>
</div>
