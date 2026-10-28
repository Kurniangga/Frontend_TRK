{{--
    resources/views/admin/partials/berita-form.blade.php
    Isi form berita, dipakai bersama oleh berita-tambah & berita-edit.
    Variabel: $action, $method ('POST'|'PUT'), $berita (array|null), $kategori, $kegiatanList, $max.
--}}
@php
    $b = $berita ?? [];
    $labelCls = 'text-[13px] font-semibold leading-[1.5] text-[#111827]';
    $selectCls = 'h-10 w-full appearance-none rounded-lg border border-[#E5E7EB] bg-white px-3 pr-10 text-sm text-[#111827] outline-none focus:border-[#2277FF] disabled:cursor-not-allowed disabled:bg-[#F3F4F6] disabled:text-[#9CA3AF]';
    $hint = 'text-xs leading-[1.5] text-[#6B7280]';
    $chev = '<svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[#6B7280]" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6 -6" /></svg>';
    $isiAwal = old('isi', $b['isi'] ?? '');
    $sumber = old('sumber', $b['sumber'] ?? 'none');
    $adaFoto = ! empty($b['foto']);
    $isPengajuan = isset($b['status']) && $b['status'] === 'Pengajuan';
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data"
      class="rounded-lg bg-white p-4 pb-6 shadow-[1px_0_3px_rgba(0,0,0,0.02),5px_2px_6px_rgba(0,0,0,0.02)]">
    @csrf
    @if ($method !== 'POST') @method($method) @endif

    <div class="flex flex-col gap-5 overflow-hidden rounded-2xl border border-[#E5E7EB] bg-white p-6">
        <p class="{{ $hint }}">Kolom bertanda opsional dapat dikosongkan.</p>

        @if ($errors->any())
            <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                Berita belum disimpan. Periksa isian yang bertanda merah di bawah.
            </div>
        @endif

        {{-- KOTAK KHUSUS: Catatan Revisi / Penolakan (Hanya muncul jika statusnya Pengajuan) --}}
        @if ($isPengajuan)
            <div class="flex flex-col gap-2.5 rounded-lg border border-[#FAD4A8] bg-[#FFF9F2] p-4">
                <label for="catatan_admin" class="text-[13px] font-semibold leading-[1.5] text-[#854700]">Catatan Revisi / Alasan Penolakan</label>
                <textarea id="catatan_admin" name="catatan_admin" rows="3" maxlength="500"
                          placeholder="Tuliskan catatan revisi atau alasan penolakan jika diperlukan..."
                          class="rounded-lg border border-[#FAD4A8] bg-white p-3 text-sm text-[#111827] outline-none focus:border-[#2277FF]">{{ old('catatan_admin', $b['catatan_admin'] ?? '') }}</textarea>
                <span class="text-xs text-[#854700]/80">Catatan ini akan dikirimkan kepada pengaju (dosen/mahasiswa) sebagai instruksi perbaikan.</span>
                @error('catatan_admin') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
            </div>
        @endif

        {{-- Sumber kegiatan --}}
        <div class="flex flex-col gap-1">
            <h3 class="text-[18px] font-bold leading-[1.4] text-[#111827]">Sumber kegiatan</h3>
            <p class="text-[13px] leading-[1.5] text-[#6B7280]">Kegiatan terkait bersifat opsional. Anda tetap dapat mencatat berita secara mandiri.</p>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:gap-5">
            @foreach ([['prev', 'Dari kegiatan sebelumnya'], ['none', 'Tanpa kegiatan sebelumnya']] as [$v, $l])
                <label class="flex flex-1 cursor-pointer items-center gap-2">
                    <input type="radio" name="sumber" value="{{ $v }}" class="peer sr-only" @checked($sumber === $v)>
                    <span class="relative size-5 shrink-0 rounded-full border border-[#A8A8A8] bg-[#F0F0F0]
                                 peer-checked:after:absolute peer-checked:after:left-1/2 peer-checked:after:top-1/2
                                 peer-checked:after:size-2.5 peer-checked:after:-translate-x-1/2 peer-checked:after:-translate-y-1/2
                                 peer-checked:after:rounded-full peer-checked:after:bg-[#2277FF] peer-checked:after:content-['']"></span>
                    <span class="text-sm font-medium leading-5 tracking-[-0.16px] text-[#111827]">{{ $l }}</span>
                </label>
            @endforeach
        </div>

        <div class="flex flex-col gap-2.5 overflow-hidden rounded-lg border border-[#E5E7EB] bg-[#F9FAFB] p-4">
            <div class="flex flex-col gap-[7px]">
                <label for="kegiatan_id" class="{{ $labelCls }}">Kegiatan sebelumnya (opsional)</label>
                <div class="relative">
                    <select id="kegiatan_id" name="kegiatan_id" class="{{ $selectCls }}">
                        <option value="">Cari dan pilih kegiatan</option>
                        @foreach ($kegiatanList as $k)
                            <option value="{{ $k['id'] }}" data-kategori="{{ $k['kategori'] }}" @selected(old('kegiatan_id', $b['kegiatan_id'] ?? '') === $k['id'])>{{ $k['nama'] }} • {{ $k['kategori'] }}</option>
                        @endforeach
                    </select>
                    {!! $chev !!}
                </div>
                <span class="{{ $hint }}">Aktif jika memilih ‘Dari kegiatan sebelumnya’</span>
                @error('kegiatan_id') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
            </div>
            <div class="flex flex-col gap-[7px]">
                <label for="kategori" class="{{ $labelCls }}">Kategori Kegiatan *</label>
                <div class="relative">
                    <select id="kategori" name="kategori" class="{{ $selectCls }}">
                        <option value="">Pilih kategori kegiatan</option>
                        @foreach ($kategori as $k)
                            <option value="{{ $k }}" @selected(old('kategori', $b['kegiatan'] ?? '') === $k)>{{ $k }}</option>
                        @endforeach
                    </select>
                    {!! $chev !!}
                </div>
                <span class="{{ $hint }}">Aktif ketika memilih ‘Tanpa kegiatan sebelumnya’. Bila memilih kegiatan sebelumnya, kategori mengikuti kegiatan itu.</span>
                @error('kategori') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="h-px bg-[#E5E7EB]"></div>

        {{-- Informasi berita --}}
        <div class="flex flex-col gap-1">
            <h3 class="text-[18px] font-bold leading-[1.4] text-[#111827]">Informasi berita</h3>
            <p class="text-[13px] leading-[1.5] text-[#6B7280]">Isikan judul dan isi berita sesuai dokumen bukti.</p>
        </div>

        <div class="flex flex-col gap-1">
            <label for="judul" class="text-[13px] font-semibold leading-[22px] tracking-[-0.18px] text-[#111827]">Judul Berita *</label>
            <input id="judul" name="judul" type="text" value="{{ old('judul', $b['judul'] ?? '') }}"
                   placeholder="Mahasiswa TRK Borong Juara di Kompetisi IT Nasional"
                   class="h-[33px] rounded-md border border-[#E4E4E7] bg-white px-[17px] text-base outline-none placeholder:text-[#B3B3B8] focus:border-[#2277FF]">
            @error('judul') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
        </div>

        <div class="flex flex-col gap-2">
            <label for="isi" class="{{ $labelCls }}">Isi Berita *</label>
            <textarea id="isi" name="isi" maxlength="{{ $max }}" placeholder="Tuliskan isi berita di sini"
                      class="min-h-[116px] resize-y rounded-lg border border-[#E5E7EB] bg-white p-3 text-sm leading-5 outline-none placeholder:text-[#6B7280] focus:border-[#2277FF]">{{ $isiAwal }}</textarea>
            <span class="{{ $hint }}">Maksimal 1.000 karakter • <span id="char-count">{{ mb_strlen($isiAwal) }}</span>/1.000</span>
            @error('isi') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
        </div>

        <div class="h-px bg-[#E5E7EB]"></div>

        {{-- Foto sampul --}}
        <div class="flex flex-col gap-1">
            <h3 class="text-[18px] font-bold leading-[1.4] text-[#111827]">Foto Sampul Berita</h3>
            <p class="text-[13px] leading-[1.5] text-[#6B7280]">Unggah foto yang menunjukkan nama, capaian, atau bukti kegiatan.</p>
        </div>

        <div class="flex flex-col items-center gap-3 overflow-hidden rounded-lg border border-dashed border-[#AAC8F8] bg-[#F9FAFB] p-6 text-center">
            <svg class="text-[#0B3A8C]" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 15m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"/><path d="M13 17.5v4.5l2 -1.5l2 1.5v-4.5"/><path d="M10 19h-5a2 2 0 0 1 -2 -2v-10c0 -1.1 .9 -2 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -1 1.99"/><path d="M6 9l12 0"/><path d="M6 12l3 0"/></svg>
            <p class="text-sm font-semibold leading-[1.5] text-[#111827]">Unggah foto bukti kegiatan{{ $adaFoto ? '' : ' *' }}</p>
            <p class="text-[13px] leading-[1.5] text-[#6B7280]">Pilih berkas dari perangkat Anda{{ $adaFoto ? '. Biarkan kosong untuk tetap memakai foto saat ini.' : '' }}</p>
            <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-[#E5E7EB] bg-white px-4 py-[11px] text-sm font-semibold text-[#0062FF]">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"/><path d="M7 9l5 -5l5 5"/><path d="M12 4l0 12"/></svg>
                Pilih berkas
                <input id="foto" name="foto" type="file" accept=".pdf,.jpg,.jpeg,.png" class="sr-only">
            </label>
            <p class="{{ $hint }}">PDF, JPG, atau PNG • Maksimal 5 MB per berkas</p>
        </div>
        <p id="file-status" class="{{ $hint }}">{{ $adaFoto ? 'Foto saat ini: '.basename($b['foto']) : 'Belum ada berkas diunggah. Pastikan foto sampul dapat dibaca dengan jelas.' }}</p>
        @error('foto') <span class="text-xs text-red-600">{{ $message }}</span> @enderror

        <div class="h-px bg-[#E5E7EB]"></div>

        {{-- Aksi --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="{{ $hint }}">Periksa kembali catatan dan isi berita sebelum menyimpan.</p>
            <div class="flex flex-wrap gap-3 text-sm font-semibold">
                <a href="{{ route('admin.berita') }}" class="flex h-10 items-center rounded-lg border border-[#E5E7EB] bg-white px-4 text-[#0062FF]">Batal</a>
                
                @if ($isPengajuan)
                    {{-- Opsi Aksi Lengkap Khusus Status Pengajuan --}}
                    <button type="submit" name="status" value="Ditolak" class="h-10 rounded-lg border border-red-300 bg-red-50 px-4 text-red-600 hover:bg-red-100">Tolak Berita</button>
                    <button type="submit" name="status" value="Draft" class="h-10 rounded-lg bg-[#FBE5CB] px-4 text-[#854700]">Kirim Revisi</button>
                    <button type="submit" name="status" value="Publish" class="h-10 rounded-lg bg-[#2277FF] px-4 text-white shadow-sm">Setujui & Publish</button>
                @else
                    {{-- Tombol standar untuk tambah/edit berita biasa --}}
                    <button type="submit" name="status" value="Draft" class="h-10 rounded-lg bg-[#CDDEF9] px-4 text-[#2277FF]">Simpan Draft</button>
                    <button type="submit" name="status" value="Publish" class="h-10 rounded-lg border border-[#2277FF] bg-[#2277FF] px-4 text-white">Publish Berita</button>
                @endif
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
(function () {
    var isi = document.getElementById('isi'), counter = document.getElementById('char-count');
    if (isi && counter) {
        isi.addEventListener('input', function () { counter.textContent = isi.value.length; });
    }

    var foto = document.getElementById('foto'), status = document.getElementById('file-status');
    if (foto && status) {
        var awal = status.textContent;
        foto.addEventListener('change', function () {
            status.textContent = foto.files.length ? 'Berkas dipilih: ' + foto.files[0].name : awal;
        });
    }

    // Aktif/nonaktif sesuai sumber kegiatan
    var radios = document.querySelectorAll('input[name="sumber"]');
    var kegiatan = document.getElementById('kegiatan_id'), kategori = document.getElementById('kategori');
    if (kegiatan && kategori) {
        function sync() {
            var checkedRadio = document.querySelector('input[name="sumber"]:checked');
            var prev = checkedRadio && checkedRadio.value === 'prev';
            kegiatan.disabled = !prev;
            kategori.disabled = prev;
            if (prev && kegiatan.selectedOptions[0] && kegiatan.selectedOptions[0].dataset.kategori) {
                kategori.value = kegiatan.selectedOptions[0].dataset.kategori;
            }
        }
        radios.forEach(function (r) { r.addEventListener('change', sync); });
        kegiatan.addEventListener('change', sync);
        sync();
    }
})();
</script>
@endpush