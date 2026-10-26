{{--
    resources/views/components/user-menu.blade.php
    Dropdown akun di header (dipakai admin DAN kaprodi): avatar + nama + peran, isi: Profil Saya, Keluar.

    Pemakaian:
      <x-user-menu :nama="session('admin.nama', 'Admin')" peran="Admin"
                   :logout="route('admin.logout')" :profil="route('admin.profil')" />
    Props:
      nama   : nama yang ditampilkan
      peran  : tulisan kecil di bawah nama (Admin / Kaprodi)
      logout : URL logout (route POST)
      profil : URL halaman profil (boleh kosong; item tetap tampil tapi tidak menuju ke mana-mana)
--}}
@props(['nama' => 'Pengguna', 'peran' => '', 'logout', 'profil' => null])

@php
    $inisial = strtoupper(mb_substr(trim($nama) ?: 'U', 0, 1));
@endphp

<details class="user-menu relative">
    <summary class="flex cursor-pointer list-none items-center gap-3 rounded-lg py-1 pr-1 outline-none marker:hidden [&::-webkit-details-marker]:hidden focus-visible:ring-2 focus-visible:ring-[#2277FF]/40">
        <span class="flex size-10 items-center justify-center rounded-full bg-[#E4C9AE] text-base font-semibold text-[#7A4A1E]">{{ $inisial }}</span>
        <span class="hidden text-left sm:block">
            <span class="block text-sm font-semibold leading-tight text-[#0A0A0A]">{{ $nama }}</span>
            <span class="block text-xs text-[#80838D]">{{ $peran }}</span>
        </span>
        <svg class="user-menu-chevron hidden text-[#55555C] transition-transform sm:block" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6 -6" /></svg>
    </summary>

    <div class="absolute right-0 top-[calc(100%+8px)] z-50 w-[260px] rounded-xl border border-[#E4E4E7] bg-white p-5 shadow-[0_8px_24px_rgba(0,0,0,0.12)]">
        <p class="text-base font-bold leading-6 text-[#0F172A]">{{ $nama }}</p>
        <p class="text-sm text-[#6B7280]">{{ $peran }}</p>

        <div class="mt-5 flex flex-col gap-1">
            <a href="{{ $profil ?: '#' }}"
               class="flex items-center gap-3 rounded-md px-1 py-2 text-base text-[#374151] hover:bg-[#F3F4F6]">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-[#6B7280]"><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
                Profil Saya
            </a>

            <form method="POST" action="{{ $logout }}">
                @csrf
                <button type="submit"
                        class="flex w-full items-center gap-3 rounded-md px-1 py-2 text-left text-base text-[#E11D2E] hover:bg-[#FEF2F2]">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" /><path d="M9 12h12l-3 -3" /><path d="M18 15l3 -3" /></svg>
                    Keluar
                </button>
            </form>
        </div>
    </div>
</details>

@once
    <style>
        .user-menu[open] .user-menu-chevron { transform: rotate(180deg); }
    </style>
    <script>
        // Tutup dropdown saat klik di luar atau tekan Esc.
        document.addEventListener('click', function (e) {
            document.querySelectorAll('details.user-menu[open]').forEach(function (d) {
                if (!d.contains(e.target)) d.removeAttribute('open');
            });
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('details.user-menu[open]').forEach(function (d) { d.removeAttribute('open'); });
            }
        });
    </script>
@endonce