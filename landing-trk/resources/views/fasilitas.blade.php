@extends('layouts.app')

@section('title', 'Fasilitas - Teknologi Rekayasa Komputer')
@section('description', 'Fasilitas Teknologi Rekayasa Komputer')

@php $first = $daftar[$aktif] ?? null; @endphp

@section('content')
<style>
    .fas-fade { transition: opacity .25s ease-out; }
    .fas-hide { opacity: 0; }
</style>

<div class="min-h-screen w-full bg-white font-['Plus_Jakarta_Sans',sans-serif] text-[#4F4F4F]">

    <x-navbar />

    <main class="mx-auto max-w-[1728px] px-5 pb-16 pt-5 lg:px-[70px]">
        <a href="{{ route('home') }}#fasilitas"
           class="mb-4 inline-flex h-[45px] items-center gap-2 rounded-full bg-[#27F] p-0.5 pr-5 text-xl font-semibold text-white">
            <span class="flex size-[42px] items-center justify-center rounded-full bg-[#5695FB]">
                <x-icon name="chevron-left" :size="24" :stroke="1.5" class="text-[#FEFEFE]" />
            </span>
            Kembali
        </a>

        @if ($first)
            <section class="rounded-[30px] bg-white p-4 shadow-[0_0_0_5px_#E3E3E3] sm:p-7">
                <div class="grid gap-8 lg:grid-cols-[minmax(0,1085fr)_minmax(0,398fr)]">

                    {{-- Foto utama --}}
                    <div class="relative aspect-[201/113] w-full overflow-hidden rounded-[20px] bg-[linear-gradient(135deg,#CDDEF9,#9DBFF0)]">
                        <img id="fas-main" src="{{ $first['foto'] }}" alt="{{ $first['nama'] }}"
                             onerror="this.style.visibility='hidden'" onload="this.style.visibility='visible'"
                             class="fas-fade absolute inset-0 h-full w-full object-cover">
                    </div>

                    {{-- Judul + keterangan --}}
                    <div id="fas-text" class="fas-fade flex flex-col gap-6">
                        <h1 id="fas-title" class="text-[28px] font-bold leading-tight tracking-[-0.6px] sm:text-4xl xl:text-5xl xl:leading-[60px]">{{ $first['nama'] }}</h1>
                        <p id="fas-desc" class="text-base font-medium leading-8 xl:text-lg">{{ $first['deskripsi'] }}</p>
                    </div>
                </div>

                {{-- Foto kecil (klik untuk mengganti foto utama + keterangan) --}}
                <div class="relative mt-6 flex items-center gap-3">
                    <button type="button" data-fas-scroll="-1" aria-label="Sebelumnya"
                            class="flex size-[42px] shrink-0 items-center justify-center rounded-full bg-[#5695FB] text-white transition-colors hover:bg-[#3F82F2]">
                        <x-icon name="chevron-left" :size="24" :stroke="1.5" />
                    </button>

                    <div id="fas-scroller" class="flex min-w-0 flex-1 gap-[25px] overflow-x-auto py-4 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                        @foreach ($daftar as $i => $f)
                            <button type="button" data-fas-thumb="{{ $i }}" data-active="{{ $i === $aktif ? 'true' : 'false' }}" aria-label="{{ $f['nama'] }}"
                                    class="shrink-0 rounded-[20px] ring-4 ring-transparent ring-offset-2 transition-all hover:opacity-90 data-[active=true]:ring-[#27F]">
                                <span class="block h-[150px] w-[270px] overflow-hidden rounded-[20px] bg-[linear-gradient(135deg,#CDDEF9,#9DBFF0)] sm:h-[190px] sm:w-[340px]">
                                    <img src="{{ $f['foto'] }}" alt="" loading="lazy" onerror="this.style.visibility='hidden'" class="h-full w-full object-cover">
                                </span>
                            </button>
                        @endforeach
                    </div>

                    <button type="button" data-fas-scroll="1" aria-label="Berikutnya"
                            class="flex size-[42px] shrink-0 items-center justify-center rounded-full bg-[#5695FB] text-white transition-colors hover:bg-[#3F82F2]">
                        <x-icon name="chevron-right" :size="24" :stroke="1.5" />
                    </button>
                </div>
            </section>
        @else
            <div class="flex min-h-[320px] flex-col items-center justify-center gap-3 rounded-[30px] bg-[#F1F6FE] px-6 text-center shadow-[0_0_0_5px_#E3E3E3]">
                <p class="text-lg font-semibold text-[#0B3A8C]">Data fasilitas belum tersedia</p>
            </div>
        @endif
    </main>
</div>
@endsection

@if ($first)
@push('scripts')
<script>
    (function () {
        var items = @json($daftar);
        var main = document.getElementById('fas-main'), text = document.getElementById('fas-text');
        var title = document.getElementById('fas-title'), desc = document.getElementById('fas-desc');
        var scroller = document.getElementById('fas-scroller');
        var thumbs = Array.prototype.slice.call(document.querySelectorAll('[data-fas-thumb]'));
        var current = {{ $aktif }}, timer = null;

        function show(i, push) {
            if (i === current && !push) return;
            current = i;
            thumbs.forEach(function (t, k) { t.dataset.active = (k === i) ? 'true' : 'false'; });
            thumbs[i].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });

            // memudar -> ganti isi -> muncul lagi
            main.classList.add('fas-hide'); text.classList.add('fas-hide');
            clearTimeout(timer);
            timer = setTimeout(function () {
                var it = items[i];
                var img = new Image();
                img.onload = img.onerror = function () {
                    main.src = it.foto; main.alt = it.nama;
                    title.textContent = it.nama; desc.textContent = it.deskripsi;
                    main.classList.remove('fas-hide'); text.classList.remove('fas-hide');
                };
                img.src = it.foto;
            }, 250);

            history.replaceState(null, '', '?f=' + i);
        }

        thumbs.forEach(function (t, k) { t.addEventListener('click', function () { show(k, false); }); });

        document.querySelectorAll('[data-fas-scroll]').forEach(function (b) {
            b.addEventListener('click', function () {
                scroller.scrollBy({ left: Number(b.dataset.fasScroll) * 365, behavior: 'smooth' });
            });
        });
    })();
</script>
@endpush
@endif
