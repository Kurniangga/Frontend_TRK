@extends('layouts.app')

@section('title', 'Fasilitas - Teknologi Rekayasa Komputer')
@section('description', 'Fasilitas Teknologi Rekayasa Komputer')

@php $first = $daftar[$aktif] ?? null; @endphp

@section('content')
    <style>
        .fas-fade {
            transition: opacity .25s ease-out;
        }

        .fas-hide {
            opacity: 0;
        }
    </style>

    <div class="flex min-h-screen flex-col bg-[#F8F9FC] font-['Plus_Jakarta_Sans',sans-serif] text-[#4F4F4F]">

        <x-navbar />

        <main class="mx-auto w-full max-w-[1080px] flex-1 px-3.5 py-2.5 sm:px-6 sm:py-3.5 lg:px-8">

            {{-- Tombol Kembali --}}
            <a href="{{ route('home') }}#fasilitas"
                class="mb-2.5 inline-flex h-[28px] items-center gap-1.5 rounded-full bg-[#27F] p-0.5 pr-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-600 sm:mb-3 sm:h-[32px] sm:gap-2 sm:pr-3.5 sm:text-sm">
                <span class="flex size-[24px] items-center justify-center rounded-full bg-[#5695FB] sm:size-[26px]">
                    <x-icon name="chevron-left" :size="13" :stroke="2.5" class="text-white" />
                </span>
                Kembali
            </a>

            @if ($first)
                {{-- Card Fasilitas --}}
                <section id="fas-card"
                    class="flex flex-col rounded-[20px] bg-white p-3.5 shadow-[0_0_0_4px_#E3E3E3] sm:rounded-[26px] sm:p-5 lg:p-6">

                    {{-- Header Mini Card (khusus HP) --}}
                    <div class="mb-2.5 flex items-center justify-between sm:hidden">
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full bg-[#EBF3FE] px-2.5 py-0.5 text-[11px] font-semibold text-[#0043AD]">
                            Fasilitas TRK
                        </span>
                        <span id="fas-counter" class="text-xs font-medium text-[#737C8D]">
                            {{ $aktif + 1 }} dari {{ count($daftar) }}
                        </span>
                    </div>

                    {{-- Grid: stacked di HP, side-by-side di desktop --}}
                    <div class="grid gap-3 sm:gap-5 lg:grid-cols-[1.25fr_1fr] lg:gap-6 items-center">

                        {{-- Foto utama --}}
                        <div
                            class="relative aspect-[16/10] w-full overflow-hidden rounded-[14px] bg-[linear-gradient(135deg,#CDDEF9,#9DBFF0)] sm:aspect-video sm:max-h-[250px] lg:max-h-[275px] sm:rounded-[18px]">
                            <img id="fas-main" src="{{ asset($first['foto']) }}" alt="{{ $first['nama'] }}"
                                onerror="this.style.visibility='hidden'" onload="this.style.visibility='visible'"
                                class="fas-fade absolute inset-0 h-full w-full object-cover">
                        </div>

                        {{-- Judul + keterangan --}}
                        <div id="fas-text" class="fas-fade flex flex-col justify-center gap-1.5 px-0.5 sm:gap-2.5 sm:px-0">
                            <div
                                class="hidden sm:inline-flex w-fit items-center gap-1.5 rounded-full bg-[#EBF3FE] px-2.5 py-0.5 text-[11px] font-semibold text-[#0043AD]">
                                Fasilitas Laboratorium & Ruang
                            </div>
                            <h1 id="fas-title"
                                class="text-lg font-bold leading-tight tracking-[-0.3px] text-[#111827] sm:text-2xl lg:text-[26px] xl:text-[28px]">
                                {{ $first['nama'] }}</h1>
                            <p id="fas-desc"
                                class="text-[13px] leading-relaxed text-[#555] sm:text-sm sm:leading-6 lg:text-[14px] lg:leading-relaxed text-[#4F4F4F]">
                                {{ $first['deskripsi'] }}</p>
                        </div>
                    </div>

                    {{-- Thumbnail slider: kotak-kotak merata memenuhi baris (tidak ada ruang kosong), dengan autoplay 5 detik
                    --}}
                    <div class="mt-3 border-t border-[#EEF2F6] pt-2.5 sm:mt-4 sm:pt-3">
                        <div class="mb-1.5 flex items-center justify-between sm:mb-2">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-[#737C8D]">Pilih Fasilitas</span>
                        </div>

                        <div class="relative flex items-center gap-2 sm:gap-2.5">
                            <button type="button" id="fas-prev" aria-label="Fasilitas sebelumnya"
                                class="flex size-7 shrink-0 items-center justify-center rounded-full bg-[#5695FB] text-white transition-colors hover:bg-[#3F82F2] sm:size-8">
                                <x-icon name="chevron-left" :size="15" :stroke="2" />
                            </button>

                            <div id="fas-scroller"
                                class="flex min-w-0 flex-1 gap-2.5 overflow-x-auto py-1.5 scroll-smooth [scrollbar-width:none] [&::-webkit-scrollbar]:hidden sm:gap-3">
                                @foreach ($daftar as $i => $f)
                                    <button type="button" data-fas-thumb="{{ $i }}"
                                        data-active="{{ $i === $aktif ? 'true' : 'false' }}" aria-label="{{ $f['nama'] }}"
                                        class="shrink-0 w-[100px] sm:w-[150px] lg:w-[160px] rounded-[12px] ring-2 ring-transparent ring-offset-2 transition-all hover:opacity-90 data-[active=true]:ring-[#2277FF] sm:rounded-[14px]">
                                        <span
                                            class="block h-[68px] w-[100px] sm:h-[84px] sm:w-[150px] lg:h-[88px] lg:w-[160px] overflow-hidden rounded-[10px] bg-[linear-gradient(135deg,#CDDEF9,#9DBFF0)] sm:rounded-[12px]">
                                            <img src="{{ asset($f['foto']) }}" alt="" loading="lazy"
                                                onerror="this.style.visibility='hidden'" class="h-full w-full object-cover">
                                        </span>
                                    </button>
                                @endforeach
                            </div>

                            <button type="button" id="fas-next" aria-label="Fasilitas berikutnya"
                                class="flex size-7 shrink-0 items-center justify-center rounded-full bg-[#5695FB] text-white transition-colors hover:bg-[#3F82F2] sm:size-8">
                                <x-icon name="chevron-right" :size="15" :stroke="2" />
                            </button>
                        </div>
                    </div>
                </section>
            @else
                <div
                    class="flex min-h-[320px] flex-col items-center justify-center gap-3 rounded-[30px] bg-white px-6 text-center shadow-[0_0_0_5px_#E3E3E3]">
                    <p class="text-lg font-semibold text-[#0B3A8C]">Data fasilitas belum tersedia</p>
                </div>
            @endif
        </main>

        {{-- Footer bersama --}}
        <x-footer />
    </div>
@endsection

@if ($first)
    @push('scripts')
        <script>
            (function () {
                var items = @json($daftar);
                var n = items.length;
                if (!n) return;

                var main = document.getElementById('fas-main'), text = document.getElementById('fas-text');
                var title = document.getElementById('fas-title'), desc = document.getElementById('fas-desc');
                var counter = document.getElementById('fas-counter');
                var scroller = document.getElementById('fas-scroller');
                var thumbs = Array.prototype.slice.call(document.querySelectorAll('[data-fas-thumb]'));
                var btnPrev = document.getElementById('fas-prev');
                var btnNext = document.getElementById('fas-next');
                var cardSection = document.getElementById('fas-card');

                var current = {{ $aktif }};
                var fadeTimer = null;
                var autoTimer = null;
                var INTERVAL = 5000; // Berpindah tiap 5 detik secara berurutan

                function show(i) {
                    current = (i + n) % n;

                    // Highlight thumbnail aktif
                    thumbs.forEach(function (t, k) {
                        t.dataset.active = (k === current) ? 'true' : 'false';
                    });

                    // Scroll thumbnail aktif ke posisi tengah (pada mode mobile)
                    if (thumbs[current] && scroller) {
                        thumbs[current].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                    }

                    if (counter) {
                        counter.textContent = (current + 1) + ' dari ' + n;
                    }

                    // Animasi transisi fade foto & teks
                    main.classList.add('fas-hide');
                    text.classList.add('fas-hide');
                    clearTimeout(fadeTimer);
                    fadeTimer = setTimeout(function () {
                        var it = items[current];
                        var img = new Image();
                        img.onload = img.onerror = function () {
                            main.src = it.foto;
                            main.alt = it.nama;
                            title.textContent = it.nama;
                            desc.textContent = it.deskripsi;
                            main.classList.remove('fas-hide');
                            text.classList.remove('fas-hide');
                        };
                        img.src = it.foto;
                    }, 200);

                    history.replaceState(null, '', '?f=' + current);
                }

                function startAuto() {
                    stopAuto();
                    if (n > 1) {
                        autoTimer = setInterval(function () {
                            show(current + 1);
                        }, INTERVAL);
                    }
                }

                function stopAuto() {
                    if (autoTimer) {
                        clearInterval(autoTimer);
                        autoTimer = null;
                    }
                }

                // Klik pada salah satu kotak thumbnail
                thumbs.forEach(function (t, k) {
                    t.addEventListener('click', function () {
                        show(k);
                        startAuto(); // reset timer 5 detik
                    });
                });

                // Tombol panah Prev & Next berganti foto secara urut
                if (btnPrev) {
                    btnPrev.addEventListener('click', function () {
                        show(current - 1);
                        startAuto();
                    });
                }
                if (btnNext) {
                    btnNext.addEventListener('click', function () {
                        show(current + 1);
                        startAuto();
                    });
                }

                // Jeda autoplay jika mouse diarahkan ke kartu, lanjut saat mouse keluar
                if (cardSection) {
                    cardSection.addEventListener('mouseenter', stopAuto);
                    cardSection.addEventListener('mouseleave', startAuto);
                }

                // Mulai pergerakan otomatis
                startAuto();
            })();
        </script>
    @endpush
@endif