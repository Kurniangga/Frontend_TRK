@props(['title', 'subtitle'])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center gap-2.5 text-center']) }}>
    <h2 class="text-2xl font-bold leading-tight tracking-tight text-[#27F] md:text-3xl">{{ $title }}</h2>
    <p class="text-sm font-semibold text-[#4B5563] md:text-base">{{ $subtitle }}</p>
    <div class="h-1 w-20 rounded-sm bg-[#FFD121]"></div>
</div>