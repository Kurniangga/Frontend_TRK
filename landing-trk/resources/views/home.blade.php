@extends('layouts.app')

@section('title', 'Teknologi Rekayasa Komputer - POLINES')
@section('description', 'Program Studi Teknologi Rekayasa Komputer, Politeknik Negeri Semarang')

@php
    /*
     * DATA SEMENTARA (hardcode). Nanti pindahkan ke controller / database
     * lalu kirim lewat return view('home', compact(...)).
     *
     * GAMBAR: URL Builder.io di bawah ini bersifat sementara (TEMP).
     * Unduh gambarnya, simpan di public/images/, lalu ganti $A dan
     * nama file dengan asset('images/nama-file.jpg').
     */
    $A = 'https://api.builder.io/api/v1/image/assets/TEMP/';
    $ph = 'a0aec7389b59c267fe9e6cb147a75e605ac97963?width=718';

    /*
     * SLIDESHOW HERO (4 slide, pindah otomatis tiap 5 detik).
     * Taruh foto di public/images/slides/ lalu isi nama filenya di 'img'.
     * Ubah 'title' dan 'text' sesuai keterangan yang diinginkan.
     */
    $heroSlides = [
        ['img' => 'images/slides/PBL.jpeg', 'title' => 'Pembelajaran Based-Learning (PBL)', 'text' => 'Mahasiswa Teknologi Rekayasa Komputer mengimplementasikan Kecerdasan Buatan dengan meciptakan Alat Pendeteksi Suhu & Kelembapan dan Program monitoring berbasis website  yang bekerjasama dengan Rumah Sakit Nasional Diponegoro (RSND).'],
        ['img' => 'images/slides/tefa.jpg', 'title' => 'Teaching Factory (TeFa)', 'text' => 'Mahasiswa Teknologi Rekayasa Komputer melaksanakan kegiatan Teaching Factory (TeFa) melalui praktik langsung pengembangan perangkat lunak dan manajemen sistem jaringan untuk mempersiapkan keterampilan teknis yang siap kerja di industri.'],
        ['img' => 'images/slides/KKL.jpg', 'title' => 'Kuliah Kerja Lapangan (KKL)', 'text' => 'Mahasiswa Teknologi Rekayasa Komputer melaksanakan Kuliah Kerja Lapangan (KKL) dengan mengunjungi AirNav Indonesia Kantor Cabang Denpasar untuk mempelajari sistem navigasi penerbangan dan teknologi komunikasi udara berbasis industri.'],
        ['img' => 'images/slides/magang.jpg', 'title' => 'Magang Industri', 'text' => 'Mahasiswa Teknologi Rekayasa Komputer melaksanakan kegiatan Magang di Paragon Technology and Innovation untuk mendapatkan pengalaman kerja nyata dan mengimplementasikan solusi teknologi dalam mendukung inovasi perusahaan.'],
    ];

    $lecturers = [
        ['Kuwat Santoso, S.T., M.Kom.', '9567e6964bd3f0ffce100af25b3aba8273c8b22e?width=718'],
        ['Ir. Prayitno, S.ST., M.T., P.hD', '37e57644e7c31fe78dcebb67c48693f8ba12eadd?width=704'],
        ['Prof. Dr. Ir. Kurnianingsih, S.T., M.T.', 'ac5345d8f63cfaad34306016a6f9a652872dbc58?width=700'],
        ['Muttabik Fathul Lathief S.Kom., M.Eng', '330414d78f05ef9846ac2f31eb9a77c6427b2203?width=700'],
        ['Tahan Prahara, S.T., M.Kom.', $ph],
        ['Wiktasari, S.T., M.Kom.', $ph],
        ['Nurseno Bayu Aji, S.Kom., M.Kom.', $ph],
        ['Tri Raharjo Yudantoro, S.Kom., M.Kom.', $ph],
        ['Muhammad Irwan Yanwari, S.Kom., M.Eng.', $ph],
        ['Mardiyono, S.Kom., M.Sc.', $ph],
    ];

    $news = [
        [
            'cls' => 'lg:col-span-2 lg:row-span-2',
            'img' => 'eea1dae43f4c4adec94cdca526899e7374b5d9b4?width=1760',
            'title' => 'Pengabdian',
            'text' => 'Tim Pengabdian Prodi Teknologi Rekayasa Komputer Jurusan Teknik Elektro Polines Memberikan Pelatihan Penggunaan Sensor Sederhana Pada Sistem Terbenam Di SMP Alam Ar-Ridho Kota Semarang',
            'big' => true,
        ],
        [
            'cls' => 'lg:row-span-2',
            'img' => 'c03aeabc41a38010ff1ed3bb6aa5185ef5db5a31?width=974',
            'title' => 'Penugasan sebagai Deputy Director Program SEAMEO SEAMOLEC',
            'text' => 'SEMARANG – Politeknik Negeri Semarang (Polines) kembali memperkuat jejaring internasional melalui',
        ],
        [
            'img' => 'fa2d7ab93ba4ebffe1bef04b7fdc1fd74963cae8?width=1024',
            'title' => 'Tim Pengabdian Prodi Teknologi Rekayasa Komputer Untuk UMKM Gemah Ripah',
            'text' => 'Pelatihan Penerapan IoT Untuk Sistem Pengasap Telur Asin Dan Pelatihan Digital Marketing ...',
        ],
        [
            'img' => 'be0a1bcac500d7ee3265969f939ef4dcbf5f4065?width=820',
            'title' => 'Penugasan sebagai Deputy Director Program SEAMEO SEAMOLEC',
            'text' => 'SEMARANG – Politeknik Negeri Semarang (Polines) kembali memperkuat jejaring internasional melalui',
        ],
        [
            'img' => '3b6a6f87a3cafeffaa2d1a16bea075664fcb7385?width=974',
            'title' => 'Pengukuhan Guru Besar POLINES',
            'text' => 'SEMARANG – Politeknik Negeri Semarang (Polines) kembali memperkuat pendidikan melalui....',
        ],
    ];

    $stats = [
        ['50+', 'Total prestasi'],
        ['20+', 'Juara nasional'],
        ['10+', 'Juara internasional'],
    ];

    $achievements = [
        ['badge' => 'Juara 1', 'color' => '#FFCA00', 'title' => 'The Winner of FIFA ASEAN CUP 2026', 'sub' => 'Timnas Indonesia', 'img' => 'ebc028afee08adce8cf5861ee20aba78e53cdebe?width=990'],
        ['badge' => 'Juara 1', 'color' => '#FFCA00', 'title' => 'Juara 1 Cybersecurity Kategori BIdang Digital & Media Technology pada OLIVIA 2026', 'sub' => 'Prestasi TRK', 'img' => '0e73c35db1e01f91b006db7ff31041acad798b8a?width=610'],
        ['badge' => 'Juara 2', 'color' => '#949494', 'title' => 'Juara 2 Elektro Expo 2026 IoT-Desain Produk Inovasi', 'sub' => 'Prestasi TRK', 'img' => 'b8cc31f5c40b1edb042ae90a5fa0f6f0d670fa43?width=610'],
        ['badge' => 'Juara 3', 'color' => '#AD5C00', 'title' => 'Juara 3 Cybersecurity Kategori BIdang Digital & Media Technology pada OLIVIA 2026', 'sub' => 'Prestasi TRK', 'img' => 'b225631a249c148729deae4d9bd444e5f998c573?width=670'],
        ['badge' => 'Juara 3', 'color' => '#AD5C00', 'title' => 'Juara 3 Lomba Jaringan Komputer Nasional', 'sub' => 'Prestasi TRK', 'img' => null],
        ['badge' => 'Finalis', 'color' => '#0B3A8C', 'title' => 'Finalis Hackathon Jawa Tengah', 'sub' => 'Prestasi TRK', 'img' => null],
        ['badge' => 'Juara 1', 'color' => '#FFCA00', 'title' => 'Juara 1 Lomba Desain UI/UX Regional', 'sub' => 'Prestasi TRK', 'img' => null],
        ['badge' => 'Medali perak', 'color' => '#949494', 'title' => 'Medali Perak Pameran Inovasi Internasional', 'sub' => 'Prestasi TRK', 'img' => null],
    ];

    $facilities = [
        ['name' => 'LAB KOMPUTER', 'img' => 'd85de1fefd9f5de7f85646e5eae18e98f4154ff2?width=720', 'cls' => 'sm:row-span-2 min-h-[320px]'],
        ['name' => 'CO-WORKING SPACE', 'img' => 'd4d26540f985d832c65d0719054314a08b288e4a?width=920', 'cls' => 'lg:col-span-2 min-h-[220px]'],
        ['name' => 'DISCUSS ROOM', 'img' => 'ef269c73505424833dd0b3a65b588799a0a7b9bd?width=640', 'cls' => 'min-h-[220px]'],
        ['name' => 'PRIVATE ROOM', 'img' => 'b85fffdbc922839aff2a4e96209ccefe9ac73fd6?width=640', 'cls' => 'min-h-[240px]'],
        ['name' => 'RUANG KELAS GEDUNG GKT', 'img' => '9cb23edb5c694e6944be6e883ae4547b5a1f42a7?width=920', 'cls' => 'lg:col-span-2 min-h-[240px]'],
    ];

    $services = [
        ['title' => 'Mahasiswa', 'kicker' => 'LAYANAN AKADEMIK', 'desc' => 'Akses informasi akademik, kegiatan, dan layanan pendukung selama masa studi di TRK', 'bg' => 'linear-gradient(108deg,#0A5BE0 1.5%,#0647B8 99%)'],
        ['title' => 'Dosen', 'kicker' => 'LAYANAN PENGAJARAN', 'desc' => 'Kelola kegiatan pengajaran dan layanan administrasi program studi dengan lebih mudah', 'bg' => 'linear-gradient(108deg,#0443AE 1.5%,#022F80 99%)'],
    ];
@endphp

@section('content')
<style>
    /* Efek masuk section saat navbar diklik: isi section naik + memudar bergantian */
    [data-reveal].reveal-hide > * { opacity: 0; transform: translateY(36px); }
    [data-reveal].reveal-play > * { animation: revealUp .8s cubic-bezier(.16, 1, .3, 1) both; }
    [data-reveal].reveal-play > *:nth-child(2) { animation-delay: .12s; }
    [data-reveal].reveal-play > *:nth-child(3) { animation-delay: .24s; }
    [data-reveal].reveal-play > *:nth-child(4) { animation-delay: .36s; }
    @keyframes revealUp { from { opacity: 0; transform: translateY(36px); } to { opacity: 1; transform: none; } }
    @media (prefers-reduced-motion: reduce) {
        [data-reveal].reveal-hide > * { opacity: 1; transform: none; }
        [data-reveal].reveal-play > * { animation: none; }
    }
</style>
<div class="font-['Plus_Jakarta_Sans',sans-serif] text-black">

    <x-navbar />

    {{-- Hero (slideshow) --}}
    <section id="hero" class="flex h-[calc(100svh-64px)] min-h-[520px] flex-col bg-[#0043AD] md:h-[calc(100svh-70px)]" aria-roledescription="carousel" aria-label="Kegiatan TRK">

        {{-- Area foto: foto bertumpuk, yang aktif tampil (fade) --}}
        <div class="relative min-h-0 flex-1 overflow-hidden bg-[linear-gradient(135deg,#0043AD,#001B47)]">
            @foreach ($heroSlides as $i => $s)
                <img src="{{ asset($s['img']) }}" alt="{{ $s['title'] }}" data-slide data-active="{{ $i === 0 ? 'true' : 'false' }}"
                     onerror="this.style.visibility='hidden'"
                     class="absolute inset-0 h-full w-full object-cover opacity-0 transition-opacity duration-1000 ease-out data-[active=true]:opacity-100">
            @endforeach

            <div class="absolute inset-0 bg-[linear-gradient(90deg,#003385_0%,rgba(34,119,255,0.05)_100%)] opacity-30"></div>

            {{-- Teks TRK (tetap, tidak ikut berganti) --}}
            <div class="absolute inset-0 flex flex-col items-center justify-center px-4 pb-8 text-center italic text-white [text-shadow:0_8px_32px_rgba(0,0,0,0.4)]">
                <div class="text-[96px] font-extrabold leading-none tracking-[6px] opacity-80 sm:text-[120px] md:text-[200px]">TRK</div>
                <div class="text-lg font-extrabold sm:text-2xl md:text-3xl">Teknologi Rekayasa Komputer</div>
            </div>

            {{-- Titik indikator --}}
            <div class="absolute inset-x-0 bottom-4 flex justify-center gap-2.5">
                @foreach ($heroSlides as $i => $s)
                    <button type="button" data-dot="{{ $i }}" data-active="{{ $i === 0 ? 'true' : 'false' }}" aria-label="Slide {{ $i + 1 }}"
                            class="h-2.5 w-2.5 rounded-full bg-white/50 transition-all duration-300 ease-out data-[active=true]:w-7 data-[active=true]:bg-[#FFD121]"></button>
                @endforeach
            </div>
        </div>

        {{-- Keterangan: semua ditumpuk di satu sel grid, tinggi mengikuti yang terpanjang --}}
        <div class="grid bg-[linear-gradient(90deg,#0043AD,#001B47)] text-[#FFD121]">
            @foreach ($heroSlides as $i => $s)
                <div data-caption data-active="{{ $i === 0 ? 'true' : 'false' }}"
                     class="col-start-1 row-start-1 flex flex-col gap-3 px-6 py-5 opacity-0 transition-opacity duration-1000 ease-out data-[active=true]:opacity-100 md:px-10">
                    <h2 class="text-xl font-bold tracking-tight md:text-3xl md:leading-[40px]">{{ $s['title'] }}</h2>
                    <p class="text-sm font-medium tracking-tight md:text-xl md:leading-[28px]">{{ $s['text'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Tentang --}}
    <section id="tentang" data-reveal class="scroll-mt-16 md:scroll-mt-[70px] border-t-[8px] border-[#FFD121] bg-white px-5 py-12 md:px-10 md:py-20 lg:px-16">
    <div class="mx-auto max-w-[1280px]">
            {{-- Header Judul --}}
            <div class="mb-12 flex flex-col items-center text-center md:mb-16">
                <h2 class="text-3xl font-bold text-[#27F] md:text-5xl">Tentang TRK</h2>
                <p class="mt-2.5 text-xs font-semibold text-gray-500 md:text-sm">Teknologi Rekayasa Komputer · Politeknik Negeri Semarang</p>
                <div class="mt-3 h-1 w-12 rounded-full bg-[#FFD121]"></div>
            </div>

            {{-- Apa itu TRK --}}
            <div class="mb-12 grid items-center gap-8 md:grid-cols-2 md:gap-12">
                <div>
                    <h3 class="mb-4 text-2xl font-bold text-[#0B3A8C] md:text-3xl">Apa itu TRK?</h3>
                    <p class="text-sm leading-relaxed text-gray-600 md:text-[15px]">
                        TRK (Teknologi Rekayasa Komputer) adalah program studi yang mempelajari perangkat keras & lunak komputer dan elektronika. Mengedepankan inovasi dan karakter, kami menumbuhkan generasi pelopor teknologi dan informasi untuk Indonesia Emas 2045.
                    </p>
                </div>
                <div class="relative aspect-[16/5] w-full overflow-hidden rounded-[20px] bg-[#E4EDFB] shadow-md">
                    <img src="{{ asset('images/mahasiswa trk.jpeg') }}" alt="Mahasiswa TRK"
                        onerror="this.style.visibility='hidden'"
                            class="absolute inset-0 h-full w-full object-cover object-center">
                </div>
            </div>

            {{-- 4 Kartu Statistik --}}
            <div class="mb-12 grid grid-cols-2 gap-4 md:grid-cols-4 md:gap-5">
                <div class="flex flex-col items-center justify-center rounded-2xl bg-[#E4EDFB] p-5 text-center shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-2 text-[#0B3A8C]"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <div class="text-xl font-black text-[#0B3A8C] md:text-2xl">400+</div>
                    <div class="mt-1 text-xs font-semibold text-gray-700">Mahasiswa aktif</div>
                </div>
                <div class="flex flex-col items-center justify-center rounded-2xl bg-[#E4EDFB] p-5 text-center shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-2 text-[#0B3A8C]"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                    <div class="text-xl font-black text-[#0B3A8C] md:text-2xl">20+</div>
                    <div class="mt-1 text-xs font-semibold text-gray-700">Dosen tetap</div>
                </div>
                <div class="flex flex-col items-center justify-center rounded-2xl bg-[#E4EDFB] p-5 text-center shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-2 text-[#0B3A8C]"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                    <div class="text-[10px] font-semibold text-gray-600">Ruang pembelajaran terapan</div>
                    <div class="mt-0.5 text-sm font-bold text-[#0B3A8C] md:text-base">Laboratorium</div>
                </div>
                <div class="flex flex-col items-center justify-center rounded-2xl bg-[#E4EDFB] p-5 text-center shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-2 text-[#0B3A8C]"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                    <div class="text-sm font-bold text-[#0B3A8C] md:text-base">Akreditasi</div>
                    <div class="mt-1 text-[11px] font-semibold text-gray-700">Baik Sekali</div>
                </div>
            </div>

            {{-- Visi & Misi --}}
            <div class="flex flex-col gap-4">
                {{-- Visi --}}
                <div class="relative overflow-hidden rounded-[20px] bg-[#0B3A8C] px-6 py-8 text-center shadow-md md:py-10">
                    {{-- Ornamen Lingkaran Kanan --}}
                    <div class="pointer-events-none absolute right-0 top-1/2 -translate-y-1/2 opacity-20">
                        <svg width="200" height="200" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="150" cy="100" r="99.5" stroke="white"/>
                            <circle cx="150" cy="100" r="69.5" stroke="white"/>
                            <circle cx="150" cy="100" r="39.5" stroke="white"/>
                        </svg>
                    </div>
                    <h4 class="relative z-10 mb-3 text-lg font-bold text-[#FFD121] md:text-xl">Visi Program Studi</h4>
                    <p class="relative z-10 mx-auto max-w-[800px] text-[13px] font-medium leading-relaxed text-white md:text-[15px]">
                        Program Studi Teknologi Rekayasa Komputer Politeknik Negeri Semarang menjadi program studi yang unggul, inovatif, dan berkarakter dalam penerapan Ilmu Pengetahuan dan Teknologi Informatika
                    </p>
                </div>

                {{-- Misi --}}
                <div class="grid gap-4 md:grid-cols-[1fr_2.5fr]">
                    <div class="flex flex-col items-center justify-center rounded-[20px] bg-[#E4EDFB] px-6 py-8 text-center shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#27F]"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="6"></circle><circle cx="12" cy="12" r="2"></circle></svg>
                        <div class="mt-4 text-[15px] font-bold text-[#0B3A8C]">Misi program studi</div>
                        <div class="mt-1 text-5xl font-black text-[#27F]">05</div>
                        <div class="mt-1 text-xs font-semibold text-gray-500">poin misi</div>
                    </div>
                    
                    <div class="flex flex-col gap-5 rounded-[20px] border border-gray-100 bg-white p-6 shadow-sm md:p-8">
                        @php
                            $misiList = [
                                'Melaksanakan pendidikan tinggi vokasi bidang teknologi informatika yang unggul, inovatif dan berkarakter.',
                                'Melaksanakan dan mengembangkan penelitian terapan dan pengabdian kepada masyarakat dalam bidang teknologi informatika yang relevan dengan kebutuhan industri dan masyarakat.',
                                'Melaksanakan tata kelola program studi melalui perbaikan berkelanjutan.',
                                'Menerapkan budaya akademik yang berkarakter.',
                                'Mengembangkan kerjasama dengan pemangku kepentingan.'
                            ];
                        @endphp
                        
                        @foreach($misiList as $index => $misi)
                            <div class="flex items-start gap-4">
                                <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-[#E4EDFB] text-xs font-bold text-[#0B3A8C]">{{ $index + 1 }}</div>
                                <p class="mt-1 text-xs font-medium leading-relaxed text-gray-700 md:text-[13px]">{{ $misi }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- Dosen --}}
<section id="dosen" data-reveal class="scroll-mt-16 md:scroll-mt-[70px] bg-[#0043AD] px-5 py-12 text-white md:px-10 md:py-16 lg:px-16">
    <div class="mx-auto max-w-[1280px]">
        <h2 class="text-2xl font-extrabold leading-tight md:text-4xl">
            DOSEN
            <span class="mt-1 block text-xl md:text-2xl">Teknologi Rekayasa Komputer</span>
        </h2>
        <p class="mt-3 max-w-[700px] text-sm font-medium leading-relaxed text-white/80 md:text-base">
            Mari mengenal lebih dekat dengan para tenaga pendidik yang ada di Program Studi Teknologi Rekayasa Komputer.
        </p>

        <div class="relative mt-8">
            <button type="button" aria-label="Sebelumnya" data-scroll="-512" class="absolute -left-4 top-1/2 z-10 hidden h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full border-[1.5px] border-white bg-[#0043AD]/70 transition-colors hover:bg-[#0043AD] md:flex">
                <x-icon name="chevron-left" />
            </button>
            <button type="button" aria-label="Berikutnya" data-scroll="512" class="absolute -right-4 top-1/2 z-10 hidden h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full border-[1.5px] border-white bg-[#0043AD]/70 transition-colors hover:bg-[#0043AD] md:flex">
                <x-icon name="chevron-right" />
            </button>

            <div id="dosen-scroller" class="flex snap-x gap-4 overflow-x-auto py-4 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                @foreach ($lecturers as [$name, $img])
                    <div class="relative h-[280px] w-[200px] shrink-0 snap-start overflow-hidden rounded-[14px] bg-white shadow-md md:h-[336px] md:w-[240px]">
                        <img src="{{ $A . $img }}" alt="{{ $name }}" class="h-full w-full object-cover">
                        <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(255,254,254,0)_45%,#000_100%)]"></div>
                        <div class="absolute inset-x-0 bottom-3 px-3 text-center font-['Poppins',sans-serif] text-[11px] font-bold leading-tight">{{ $name }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

    {{-- Kabar --}}
<section id="kabar" data-reveal class="scroll-mt-16 md:scroll-mt-[70px] border-t-[10px] border-[#FFD121] bg-[#E4EDFB] px-5 py-8 md:px-10 md:py-10 lg:px-16">
    <div class="mx-auto flex max-w-[1280px] flex-col gap-6">
        <x-section-header title="Kabar Terbaru TRK" subtitle="Ikuti perkembangan terkini seputar penelitian, kegiatan akademik, dan seputar informasi penting lainnya" />

        <div class="grid gap-4 sm:grid-cols-2 lg:auto-rows-[clamp(190px,calc((100svh_-_320px)/2),320px)] lg:grid-cols-3">
            @foreach ($news as $n)
                <article class="relative min-h-[260px] overflow-hidden rounded-[16px] bg-white shadow-sm md:min-h-[280px] lg:min-h-0 {{ $loop->first ? 'lg:col-span-2' : '' }}">
                    <img src="{{ $A . $n['img'] }}" alt="{{ $n['title'] }}" class="absolute inset-0 h-full w-full object-cover">
                    <div class="absolute inset-x-0 bottom-0 flex flex-col gap-1 bg-[linear-gradient(180deg,rgba(255,209,33,0)_0%,#FFD121_40%)] px-4 pb-3 pt-10">
                        <h3 class="{{ !empty($n['big']) ? 'text-base md:text-lg' : 'text-xs md:text-sm' }} font-bold leading-tight text-black">{{ $n['title'] }}</h3>
                        <p class="line-clamp-2 text-[11px] leading-snug tracking-tight text-gray-900 md:text-xs {{ !empty($n['big']) ? 'font-semibold' : 'font-medium' }}">{{ $n['text'] }}</p>
                        <a href="#kabar" class="mt-1 text-[11px] font-bold text-black hover:underline md:text-xs">Baca Selengkapnya</a>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

    {{-- Prestasi --}}
    <section id="prestasi" data-reveal class="scroll-mt-16 md:scroll-mt-[70px] border-b-[20px] border-[#FFD121] bg-white px-4 pb-[70px] pt-[70px] md:px-[60px] md:pt-[110px] xl:px-[224px]">
        <div class="mx-auto flex max-w-[1280px] flex-col items-center gap-[30px]">
            <x-section-header title="Prestasi" subtitle="Capaian mahasiswa Teknologi Rekayasa Komputer · Politeknik Negeri Semarang" />

            <div class="grid w-full grid-cols-1 rounded-2xl bg-[#CDDEF9] py-5 font-['Poppins',sans-serif] shadow-[2px_5px_15px_4px_rgba(0,0,0,0.25)] sm:grid-cols-3">
                @foreach ($stats as $i => [$num, $label])
                    <div class="flex flex-col items-center gap-1 py-2 {{ $i === 1 ? 'sm:border-x sm:border-[#DCE6F7]' : '' }}">
                        <div class="text-[40px] font-medium leading-[48px] text-[#0B3A8C]">{{ $num }}</div>
                        <div class="text-base text-[#1F6FEB]">{{ $label }}</div>
                    </div>
                @endforeach
            </div>

            <div class="grid w-full gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($achievements as $a)
                    <div class="relative h-[381px] overflow-hidden rounded-2xl bg-[#DCE8FA] font-['Poppins',sans-serif]">
                        @if ($a['img'])
                            <img src="{{ $A . $a['img'] }}" alt="" class="absolute inset-0 h-full w-full object-cover">
                        @else
                            <svg class="absolute left-1/2 top-16 -translate-x-1/2" width="120" height="140" viewBox="0 0 120 140" fill="none" aria-hidden="true">
                                <path d="M60 72C76.5685 72 90 58.5685 90 42C90 25.4315 76.5685 12 60 12C43.4315 12 30 25.4315 30 42C30 58.5685 43.4315 72 60 72Z" fill="#9DBFF0" />
                                <path d="M8 140C8 106 32 84 60 84C88 84 112 106 112 140H8Z" fill="#9DBFF0" />
                            </svg>
                        @endif

                        <span class="absolute left-3 top-3 rounded-full px-3 py-1 text-sm font-medium text-white" style="background: {{ $a['color'] }}">{{ $a['badge'] }}</span>

                        <div class="absolute inset-x-0 bottom-0 flex min-h-[100px] flex-col justify-center gap-1 p-4 text-[#0B3A8C] {{ $a['img'] ? 'bg-[linear-gradient(180deg,rgba(153,125,20,0)_-70%,#FFD121_21%)]' : 'bg-[#F5C400]' }}">
                            <div class="text-lg font-medium leading-6">{{ $a['title'] }}</div>
                            <div class="text-sm">{{ $a['sub'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="button" class="h-[52px] rounded-xl border-2 border-[#1F6FEB] px-7 font-['Poppins',sans-serif] text-base font-medium text-[#1F6FEB]">Muat lebih banyak</button>
        </div>
    </section>

    {{-- Fasilitas --}}
<section id="fasilitas" data-reveal class="scroll-mt-16 md:scroll-mt-[70px] bg-[#0043AD] px-5 py-12 text-white md:px-10 md:py-16 lg:px-16">
    <div class="mx-auto max-w-[1280px]">
        <h2 class="text-3xl font-extrabold leading-tight md:text-5xl">
            FASILITAS
            <span class="mt-1 block text-xl md:text-3xl">Teknologi Rekayasa Komputer</span>
        </h2>
        <p class="mt-3 max-w-[600px] text-sm font-medium leading-relaxed text-white/80 md:text-base">
            Bukan sekadar ruang belajar biasa, tempat ini dirancang untuk mewadahi setiap ide dan potensi besar mahasiswa.
        </p>

        <div class="mt-8 grid gap-4 sm:grid-cols-2 md:grid-cols-3">
            @foreach ($facilities as $i => $f)
                <div class="relative overflow-hidden rounded-[14px] bg-white shadow-md {{ $i === 0 ? 'min-h-[220px] md:row-span-2 md:min-h-full' : 'aspect-[367/192]' }}">
                    <img src="{{ $A . $f['img'] }}" alt="{{ $f['name'] }}" class="absolute inset-0 h-full w-full object-cover">
                    <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(255,254,254,0)_40%,#000_100%)] opacity-90"></div>
                    <div class="absolute bottom-3 left-4 text-[11px] font-bold tracking-wide md:text-xs">{{ $f['name'] }}</div>
                </div>
            @endforeach
        </div>

        <div class="mt-10 flex justify-center">
            <a href="#fasilitas" class="inline-flex items-center gap-2 rounded-lg bg-[#27F] px-6 py-2.5 text-sm font-semibold tracking-wide transition-colors hover:bg-blue-600">
                SELENGKAPNYA <x-icon name="arrow-right" :size="18" :stroke="2" />
            </a>
        </div>
    </div>
</section>

    {{-- Layanan --}}
    <section id="layanan" data-reveal class="scroll-mt-16 md:scroll-mt-[70px] border-t-[8px] border-[#FFD121] bg-[#F2F6FD] px-4 py-8 md:py-10">
        <div class="mx-auto flex max-w-[900px] flex-col gap-6">
            <x-section-header title="Layanan" subtitle="Pusat layanan terpadu Teknologi Rekayasa Komputer · Politeknik Negeri Semarang" />

            {{-- Banner Kuning --}}
            <div class="flex flex-col items-start gap-2.5 rounded-[16px] bg-[#FFC928] px-6 py-5 shadow-sm">
                <h3 class="text-xl font-extrabold text-[#023389] md:text-2xl">Ingin kuliah di Teknologi Rekayasa Komputer?</h3>
                <p class="max-w-[700px] text-xs font-semibold leading-relaxed text-[#043DA0]">
                    Informasi mengenai program studi, persyaratan, dan tahapan pendaftaran dapat Anda pelajari terlebih dahulu sebelum mengajukan pendaftaran sebagai mahasiswa baru.
                </p>
                <a href="#layanan" class="mt-1 inline-flex items-center gap-1.5 rounded-lg bg-[#0442AC] px-4 py-2 text-[13px] font-bold text-white transition-colors hover:bg-blue-800">
                    Daftar Sekarang <x-icon name="arrow-right" :size="14" :stroke="2.5" />
                </a>
            </div>

            {{-- Kartu Biru --}}
            <div class="grid gap-4 md:grid-cols-2">
                @foreach ($services as $s)
                    <a href="#layanan" class="relative flex flex-col justify-between overflow-hidden rounded-[14px] px-5 py-4 text-white shadow-md transition-transform hover:-translate-y-1" style="background: {{ $s['bg'] }}">
                        <div class="flex flex-col gap-3">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-[10px] bg-[#FFC928] text-[#023389]">
                                    @if($s['title'] == 'Mahasiswa')
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3L1 9L4 10.63V17.5C4 18.88 7.58 20 12 20C16.42 20 20 18.88 20 17.5V10.63L23 9L12 3ZM12 18C8.69 18 6 17.1 6 16V11.72L12 15L18 11.72V16C18 17.1 15.31 18 12 18ZM12 13L3.72 8.5L12 4L20.28 8.5L12 13Z"/></svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M20 3H4C2.9 3 2 3.9 2 5V15C2 16.1 2.9 17 4 17H8V19H16V17H20C21.1 17 22 16.1 22 15V5C22 3.9 21.1 3 20 3ZM20 15H4V5H20V15ZM12 11L15.5 15H8.5L12 11Z"/></svg>
                                    @endif
                                </div>
                                <div>
                                    <div class="text-lg font-bold leading-tight">{{ $s['title'] }}</div>
                                    <div class="text-[9px] font-bold uppercase tracking-wider text-blue-200">{{ $s['kicker'] }}</div>
                                </div>
                            </div>
                            <p class="text-[11px] leading-relaxed text-blue-100">{{ $s['desc'] }}</p>
                        </div>
                        
                        <div class="mt-4 flex flex-col gap-2">
                            <div class="relative flex items-center justify-between">
                                <div class="h-1.5 w-1.5 rounded-full bg-white"></div>
                                <div class="h-px flex-1 bg-white/40"></div>
                                <div class="h-1.5 w-1.5 rounded-full bg-white"></div>
                            </div>
                            <div class="flex items-center justify-end gap-1 text-[11px] font-bold tracking-wide hover:underline">
                                Masuk Layanan <x-icon name="arrow-right" :size="12" :stroke="2.5" />
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <x-footer />
</div>
@endsection

@push('scripts')
<script>
    // Tombol panah carousel dosen
    const scroller = document.getElementById('dosen-scroller');
    document.querySelectorAll('[data-scroll]').forEach((btn) => {
        btn.addEventListener('click', () => {
            scroller.scrollBy({ left: Number(btn.dataset.scroll), behavior: 'smooth' });
        });
    });
</script>
@endpush

@push('scripts')
<script>
    // Slideshow hero: ganti slide tiap 5 detik (fade ease-out)
    (function () {
        var INTERVAL = 5000;
        var q = function (s) { return document.querySelectorAll(s); };
        var imgs = q('[data-slide]'), caps = q('[data-caption]'), dots = q('[data-dot]');
        var n = imgs.length, cur = 0, timer = null;
        if (n < 2) return;

        function show(i) {
            cur = (i + n) % n;
            [imgs, caps, dots].forEach(function (list) {
                list.forEach(function (el, k) { el.dataset.active = (k === cur) ? 'true' : 'false'; });
            });
        }
        function start() {
            clearInterval(timer);
            timer = setInterval(function () { show(cur + 1); }, INTERVAL);
        }
        dots.forEach(function (d) {
            d.addEventListener('click', function () { show(parseInt(d.dataset.dot, 10)); start(); });
        });
        start();
    })();
</script>
@endpush

@push('scripts')
<script>
    // Klik menu navbar -> scroll ke section, lalu isinya masuk dengan animasi
    (function () {
        function play(sec) {
            if (!sec || sec.dataset.revealing) return;
            sec.dataset.revealing = '1';
            var done = false;
            function go() {
                if (done) return; done = true;
                sec.classList.remove('reveal-hide');
                void sec.offsetWidth; // restart animasi
                sec.classList.add('reveal-play');
                setTimeout(function () { sec.classList.remove('reveal-play'); delete sec.dataset.revealing; }, 1300);
            }
            sec.classList.remove('reveal-play');
            sec.classList.add('reveal-hide');
            // tunggu scroll berhenti (scrollend), cadangan 900 ms jika tidak ada scroll
            window.addEventListener('scrollend', go, { once: true });
            setTimeout(go, 900);
        }

        document.addEventListener('click', function (e) {
            var a = e.target.closest('a[href*="#"]');
            if (!a || a.pathname !== location.pathname) return;
            var sec = a.hash.length > 1 && document.querySelector('[data-reveal]' + a.hash);
            if (!sec) return;
            var menu = a.closest('details'); // tutup menu hamburger di layar kecil
            document.querySelectorAll('header details[open]').forEach(function (d) { d.open = false; });
            play(sec);
        });

        // Datang dari halaman lain dengan alamat /#dosen dst.
        if (location.hash.length > 1) {
            var s = document.querySelector('[data-reveal]' + location.hash);
            if (s) window.addEventListener('load', function () { play(s); });
        }
    })();
</script>
@endpush