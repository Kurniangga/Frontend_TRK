@foreach ($stats as $s)
    <div class="flex h-[56px] flex-1 flex-col justify-end overflow-hidden rounded-[10px] bg-white text-center shadow-[0_6px_16px_rgba(0,50,136,0.08)]">
        <div class="text-[22px] font-bold leading-7 tracking-[-0.3px] text-[#2174F5]">{{ $s['value'] }}</div>
        <div class="flex h-[18px] items-center justify-center text-[11px] font-medium tracking-[-0.18px] text-[#5B6B86]">{{ $s['label'] }}</div>
        <div class="h-[3px] bg-[#FFC928]"></div>
    </div>
@endforeach