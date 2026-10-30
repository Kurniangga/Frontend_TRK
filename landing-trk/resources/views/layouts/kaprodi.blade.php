<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex">
    <title>@yield('title', 'Dashboard') · Kaprodi TRK</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo_trk remove.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo_trk remove.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        // Skala panel otomatis: lebar kerja panel dibuat setara 1440px di semua ukuran laptop.
        (function () {
            var BASE = 1440, MIN = 0.75, MAX = 1.6;   // ubah BASE untuk memperbesar/memperkecil semua halaman
            function skala() {
                var w = window.innerWidth;
                document.documentElement.style.zoom = w < 1024 ? 1 : Math.min(MAX, Math.max(MIN, w / BASE));
            }
            skala();
            window.addEventListener('resize', skala);
        })();
    </script>
</head>
<body class="bg-[#F5F5F5] font-['Plus_Jakarta_Sans',sans-serif] text-[#111] antialiased">

    {{-- Sidebar mobile dibuka/tutup lewat checkbox ini (tanpa JavaScript) --}}
    <input type="checkbox" id="kaprodi-menu" class="peer hidden">

    <aside class="fixed inset-y-0 left-0 z-40 flex w-60 -translate-x-full flex-col bg-[linear-gradient(180deg,#1F66E5_0%,#1550BD_50%,#0B2F7A_100%)] text-white shadow-[2px_0_12px_rgba(0,0,0,0.18)] transition-transform peer-checked:translate-x-0 lg:translate-x-0 lg:shadow-none">
        <a href="{{ route('kaprodi.dashboard') }}" class="flex h-[70px] shrink-0 items-center justify-center px-6">
            {{-- Logo: public/images/logo_trk.png (dibuat putih lewat filter supaya terbaca di atas sidebar biru) --}}
            <img src="{{ asset('images/logo-trk new.png') }}" alt="Teknologi Rekayasa Komputer" class="h-13 w-auto object-contain">
        </a>
        <x-kaprodi.sidebar />
    </aside>

    <label for="kaprodi-menu" class="fixed inset-0 z-30 hidden bg-black/40 peer-checked:block lg:peer-checked:hidden" aria-label="Tutup menu"></label>

    <div class="relative min-w-0 lg:ml-60">
        {{-- Header --}}
        <header class="sticky top-0 z-20 flex h-[70px] items-center gap-4 bg-white px-3">
            <label for="kaprodi-menu" class="flex size-9 shrink-0 cursor-pointer items-center justify-center rounded-md border border-[#E4E4E7] lg:hidden" aria-label="Buka menu">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </label>

            <svg class="ml-auto shrink-0" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" /><path d="M9 17v1a3 3 0 0 0 6 0v-1" /></svg>

            <div class="shrink-0 pr-4">
                <x-user-menu :nama="session('kaprodi.nama', 'Kaprodi TRK')" peran="Kaprodi"
             :logout="route('kaprodi.logout')" :profil="route('kaprodi.profil')" />
            </div>
        </header>

        <div class="relative min-w-0">
            @yield('content')
        </div>
    </div>

    @stack('scripts')
</body>
</html>