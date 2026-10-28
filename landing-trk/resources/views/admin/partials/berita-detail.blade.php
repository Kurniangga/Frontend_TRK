{{--
    resources/views/admin/partials/berita-detail.blade.php
    Isi detail berita, dipakai admin DAN kaprodi (bedanya hanya $prefix: 'admin' | 'kaprodi'). Tombol Ubah muncul bila route {prefix}.berita.edit ada.
    Variabel: $prefix, $berita, $paragraf, $foto, $hariTanggal, $tanggalPendek, $waktuBaca, $terkait.
--}}
@php
    $statusCls = $berita['status'] === 'Publish'
        ? 'border-[#AAC8F8] bg-[#CDDEF9] text-[#2277FF]'
        : 'border-[#E4E4E7] bg-[#EDEDF0] text-[#55555C]';
    $info = [
        ['Kategori', $berita['kegiatan']],
        ['Hari / Tanggal', $hariTanggal],
        ['Waktu Baca', $waktuBaca],
    ];
    $urlEdit = \Illuminate\Support\Facades\Route::has($prefix.'.berita.edit')
        ? route($prefix.'.berita.edit', $berita['id']) : null;
    $showRoute = $prefix.'.berita.show';
@endphp

<div class="flex flex-col gap-9">
    {{-- Judul + aksi --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <h1 class="max-w-[900px] text-[26px] font-semibold leading-[38px] tracking-[-0.2px]">{{ $berita['judul'] }}</h1>
        <div class="flex items-center gap-3">
            <span class="inline-flex rounded-md border px-2.5 py-1 text-sm font-medium {{ $statusCls }}">{{ $berita['status'] }}</span>
            @if ($urlEdit)
                <a href="{{ $urlEdit }}" class="flex items-center gap-1.5 rounded-md bg-[#FBE5CB] px-3 py-1.5 text-sm font-medium text-[#C77700]">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h4L18.5 9.5a2.828 2.828 0 10-4-4L4 16v4M13.5 6.5l4 4"/></svg>
                    Ubah
                </a>
            @endif
        </div>
    </div>

    {{-- Meta penulis --}}
    <div class="flex flex-wrap items-center gap-x-5 gap-y-3 py-1">
        <div class="flex size-11 items-center justify-center rounded-full bg-[#E3E3E3]">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2B3F6C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"/><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"/></svg>
        </div>
        <div class="w-40">
            <div class="text-lg font-medium leading-7 text-[#0A0A0A]">Humas TRK</div>
            <div class="text-sm text-[#7F7F7F]">Penulis</div>
        </div>
        <div class="w-40">
            <div class="text-lg font-medium leading-7 text-[#0A0A0A]">{{ $tanggalPendek }}</div>
            <div class="text-sm text-[#7F7F7F]">Tanggal publikasi</div>
        </div>
    </div>

    <div class="flex flex-col items-start gap-9 xl:flex-row">

        {{-- Artikel --}}
        <article class="flex min-w-0 flex-1 flex-col gap-[25px]">
            <div class="flex h-[240px] w-full items-center justify-center overflow-hidden rounded-lg bg-[#F3F4F6] md:h-[420px]">
                @if ($foto)
                    <img src="{{ $foto }}" alt="Foto sampul berita" class="h-full w-full object-cover">
                @else
                    <span class="px-6 text-center text-sm text-[#7D8999]">{{ $berita['foto'] ? 'Sampul berupa dokumen PDF (tidak dapat ditampilkan sebagai gambar).' : 'Belum ada foto sampul.' }}</span>
                @endif
            </div>

            <div class="flex flex-col gap-6 text-justify text-lg font-medium leading-[30px]">
                @foreach ($paragraf as $p)
                    <p>{{ $p }}</p>
                @endforeach
            </div>
        </article>

        {{-- Sidebar --}}
        <aside class="flex w-full shrink-0 flex-col gap-10 xl:w-[380px]">
            <div class="flex flex-col gap-3 rounded-[10px] border border-[#E3E3E3] bg-white px-[22px] pb-[22px] pt-[28px]">
                <h2 class="text-[28px] font-bold leading-[34px] tracking-[-0.2px] text-[#071E38]">Informasi</h2>
                @foreach ($info as [$label, $value])
                    <div class="flex flex-col gap-1.5">
                        <div class="text-base font-semibold tracking-[-0.18px] text-[#64758A]">{{ $label }}</div>
                        <div class="text-xl font-medium leading-6 text-[#071E38]">{{ $value }}</div>
                    </div>
                @endforeach
            </div>

            <div class="px-1">
                <h2 class="text-2xl font-bold leading-[30px] tracking-[-0.15px] text-[#071E38]">Informasi Terkait</h2>
                <div class="h-1 bg-gradient-to-r from-[#3979BA] to-[#9CB7D5]"></div>

                <div class="flex flex-col gap-[18px] pt-[18px]">
                    @forelse ($terkait as $t)
                        <a href="{{ route($showRoute, $t['id']) }}" class="flex items-center gap-[11px] overflow-hidden">
                            <div class="flex h-[72px] w-[110px] shrink-0 items-center justify-center overflow-hidden rounded-lg bg-[#F3F4F6] text-[#A8A8A8]">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 6h3a1 1 0 0 1 1 1v11a2 2 0 0 1 -4 0v-13a1 1 0 0 0 -1 -1h-10a1 1 0 0 0 -1 1v12a3 3 0 0 0 3 3h11"/><path d="M8 8l4 0"/><path d="M8 12l4 0"/><path d="M8 16l4 0"/></svg>
                            </div>
                            <div class="flex min-w-0 flex-col gap-1">
                                <div class="line-clamp-2 text-base font-bold leading-[22px] tracking-[-0.18px] text-[#071E38]">{{ $t['judul'] }}</div>
                                <div class="text-xs text-[#7D8999]">{{ $t['tanggal'] }}</div>
                            </div>
                        </a>
                    @empty
                        <p class="text-sm text-[#7D8999]">Belum ada berita terkait.</p>
                    @endforelse
                    <div class="h-px bg-[#E6E9EE]"></div>
                </div>
            </div>
        </aside>
    </div>
</div>
