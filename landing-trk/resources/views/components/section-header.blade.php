@props(['title', 'subtitle'])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center gap-2.5 text-center']) }}>
    <h2 class="text-[32px] font-bold leading-tight tracking-tight text-[#27F] md:text-5xl md:leading-[58px]">{{ $title }}</h2>
    <p class="text-base font-semibold text-[#4B5563] md:text-xl">{{ $subtitle }}</p>
    <div class="h-1 w-20 rounded-sm bg-[#FFD121]"></div>
</div>