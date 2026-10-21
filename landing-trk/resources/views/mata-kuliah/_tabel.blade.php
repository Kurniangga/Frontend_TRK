<section class="matkul-fade overflow-hidden rounded-[16px] bg-white shadow-[2px_5px_15px_4px_rgba(0,0,0,0.25)]">
    <div class="flex items-center gap-2.5 px-4 pb-3 pt-4">
        <h2 class="text-base font-bold leading-6 tracking-[-0.15px] text-[#0647B8]">Semester {{ $semester }}</h2>
        <span class="font-['Poppins',sans-serif] rounded-full bg-[#FFC928] px-2.5 py-0.5 text-[10px] font-bold text-[#0B2A6B]">{{ count($courses) }} Mata Kuliah</span>
    </div>

    <div class="overflow-x-auto px-3 pb-3">
        <table class="w-full min-w-[680px] table-fixed border-collapse text-center">
            <colgroup>
                <col class="w-[7%]">
                <col class="w-[25%]">
                <col class="w-[35%]">
                <col class="w-[8.25%]">
                <col class="w-[8.25%]">
                <col class="w-[8.25%]">
                <col class="w-[8.25%]">
            </colgroup>
            <thead class="bg-[linear-gradient(90deg,#0A5BE0,#00235C)] text-white">
                <tr class="h-7 text-xs font-semibold lg:text-sm">
                    <th rowspan="2" class="border-r border-white/20 text-xs font-semibold lg:text-base">No</th>
                    <th rowspan="2" class="border-r border-white/20 text-xs font-semibold lg:text-base">Kode MK</th>
                    <th rowspan="2" class="border-r border-white/20 text-xs font-semibold lg:text-base">Nama Mata Kuliah</th>
                    <th colspan="2" class="border-r border-white/20 font-semibold">SKS</th>
                    <th colspan="2" class="font-semibold">JAM</th>
                </tr>
                <tr class="h-6 text-[11px] lg:text-xs">
                    <th class="font-semibold">Teori</th>
                    <th class="font-semibold">Praktek</th>
                    <th class="border-l border-white/20 font-semibold">Teori</th>
                    <th class="font-semibold">Praktek</th>
                </tr>
            </thead>

            <tbody class="text-xs font-semibold lg:text-sm tracking-[-0.4px] text-[#00235C]">
                @forelse ($courses as $c)
                    <tr class="h-9 lg:h-[40px] [&>td]:border-r [&>td]:border-white [&>td:last-child]:border-r-0 {{ $loop->even ? 'bg-[#D9D9D9]' : 'bg-white' }}">
                        <td class="text-[#848BAB]">{{ $loop->iteration }}</td>
                        <td>{{ $c['kode'] }}</td>
                        <td class="px-3">{{ $c['nama'] }}</td>
                        <td>{{ $c['sks_teori'] }}</td>
                        <td>{{ $c['sks_praktek'] }}</td>
                        <td>{{ $c['jam_teori'] }}</td>
                        <td>{{ $c['jam_praktek'] }}</td>
                    </tr>
                @empty
                    <tr class="h-[56px] bg-white">
                        <td colspan="7" class="text-sm font-medium text-[#6B7A99]">Belum ada mata kuliah untuk semester ini.</td>
                    </tr>
                @endforelse

                {{-- Baris sub total --}}
                <tr class="h-9 bg-[#FFF4CC] text-xs font-bold lg:text-sm [&>td]:border-r [&>td]:border-white [&>td:last-child]:border-r-0">
                    <td colspan="3" class="pl-5 text-left">Sub Total Semester {{ $semester }}</td>
                    <td>{{ $total['sks_teori'] }}</td>
                    <td>{{ $total['sks_praktek'] }}</td>
                    <td>{{ $total['jam_teori'] }}</td>
                    <td>{{ $total['jam_praktek'] }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</section>