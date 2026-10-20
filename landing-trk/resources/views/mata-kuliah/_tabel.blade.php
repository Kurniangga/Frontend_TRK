<section class="matkul-fade overflow-hidden rounded-[20px] bg-white shadow-[2px_5px_15px_4px_rgba(0,0,0,0.25)]">
    <div class="flex items-center gap-3 px-[18px] pb-[30px] pt-5">
        <h2 class="text-2xl font-bold leading-[30px] tracking-[-0.15px] text-[#0647B8]">Semester {{ $semester }}</h2>
        <span class="font-['Poppins',sans-serif] rounded-full bg-[#FFC928] px-3 py-1 text-xs font-bold text-[#0B2A6B]">{{ count($courses) }} Mata Kuliah</span>
    </div>

    <div class="overflow-x-auto px-[11px] pb-[19px]">
        <table class="w-full min-w-[900px] table-fixed border-collapse text-center">
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
                <tr class="h-[43px] text-lg font-semibold lg:text-2xl">
                    <th rowspan="2" class="border-r border-white/20 text-xl font-semibold lg:text-[32px]">No</th>
                    <th rowspan="2" class="border-r border-white/20 text-xl font-semibold lg:text-[32px]">Kode MK</th>
                    <th rowspan="2" class="border-r border-white/20 text-xl font-semibold lg:text-[32px]">Nama Mata Kuliah</th>
                    <th colspan="2" class="border-r border-white/20 font-semibold">SKS</th>
                    <th colspan="2" class="font-semibold">JAM</th>
                </tr>
                <tr class="h-[43px] text-base lg:text-[22px]">
                    <th class="font-semibold">Teori</th>
                    <th class="font-semibold">Praktek</th>
                    <th class="border-l border-white/20 font-semibold">Teori</th>
                    <th class="font-semibold">Praktek</th>
                </tr>
            </thead>

            <tbody class="text-base font-semibold lg:text-2xl tracking-[-0.4px] text-[#00235C]">
                @forelse ($courses as $c)
                    <tr class="h-14 lg:h-[73px] [&>td]:border-r [&>td]:border-white [&>td:last-child]:border-r-0 {{ $loop->even ? 'bg-[#D9D9D9]' : 'bg-white' }}">
                        <td class="text-[#848BAB]">{{ $loop->iteration }}</td>
                        <td>{{ $c['kode'] }}</td>
                        <td class="px-3">{{ $c['nama'] }}</td>
                        <td>{{ $c['sks_teori'] }}</td>
                        <td>{{ $c['sks_praktek'] }}</td>
                        <td>{{ $c['jam_teori'] }}</td>
                        <td>{{ $c['jam_praktek'] }}</td>
                    </tr>
                @empty
                    <tr class="h-[73px] bg-white">
                        <td colspan="7" class="text-lg font-medium text-[#6B7A99]">Belum ada mata kuliah untuk semester ini.</td>
                    </tr>
                @endforelse

                {{-- Baris sub total --}}
                <tr class="h-[55px] bg-[#FFF4CC] text-base font-bold lg:text-xl [&>td]:border-r [&>td]:border-white [&>td:last-child]:border-r-0">
                    <td colspan="3" class="pl-6 text-left">Sub Total Semester {{ $semester }}</td>
                    <td>{{ $total['sks_teori'] }}</td>
                    <td>{{ $total['sks_praktek'] }}</td>
                    <td>{{ $total['jam_teori'] }}</td>
                    <td>{{ $total['jam_praktek'] }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</section>
