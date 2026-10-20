@foreach ($stats as $s)
    <div class="flex h-[89px] flex-1 flex-col justify-end overflow-hidden rounded-[14px] bg-white text-center shadow-[0_6px_16px_rgba(0,50,136,0.08)]">
        <div class="text-[40px] font-bold leading-[48px] tracking-[-0.3px] text-[#2174F5]">{{ $s['value'] }}</div>
        <div class="flex h-[29px] items-center justify-center text-sm font-medium tracking-[-0.18px] text-[#5B6B86] sm:text-base">{{ $s['label'] }}</div>
        <div class="h-1 bg-[#FFC928]"></div>
    </div>
@endforeach
