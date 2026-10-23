{{--
    resources/views/admin/kurikulum.blade.php
    Halaman Kurikulum TRK (admin). Tambah / ubah / ubah status / hapus langsung tersimpan lewat
    AdminKurikulumController -> KurikulumStore (JSON). Halaman publik ikut berubah.
--}}
@extends('layouts.admin')

@section('title', 'Kurikulum TRK')

@section('content')
<main class="p-4 pb-10 sm:p-6">
    <h1 class="mt-[6px] text-[32px] font-bold leading-10 tracking-[-0.3px]">Kurikulum TRK</h1>
    <x-admin.breadcrumb :items="[['Dashboard', 'admin.dashboard'], ['Kurikulum'], ['Kurikulum TRK']]" />

    <section class="mt-6 rounded-lg bg-white p-6 shadow-[0_1px_4px_rgba(0,0,0,0.06)]">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="text-lg font-medium">Daftar Kurikulum</h2>
                <p class="text-sm text-[#8A8A90]">Kurikulum yang digunakan oleh program studi</p>
            </div>
            <button id="btnAdd" type="button" class="flex h-10 items-center gap-2 rounded-md bg-[#1F73FF] px-4 text-base text-white">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                Kurikulum
            </button>
        </div>

        {{-- Pencarian + filter status --}}
        <div class="mt-5 flex flex-wrap gap-4">
            <div class="relative min-w-[220px] flex-1">
                <svg class="absolute left-3.5 top-2.5 text-[#8A8A90]" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                <input id="q" placeholder="Cari nama kurikulum..." class="h-10 w-full rounded-md border border-[#E4E4E7] bg-white pl-[45px] pr-3 text-base outline-none placeholder:text-[#B3B3B8]">
            </div>
            <div class="relative w-full sm:w-[220px]">
                <select id="statusFilter" class="h-10 w-full appearance-none rounded-md border border-[#D4D4D8] bg-white px-[17px] text-base outline-none">
                    <option value="all">Semua status</option>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
                <svg class="pointer-events-none absolute right-3 top-[11px]" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6l6 -6" /></svg>
            </div>
        </div>

        {{-- Tabel --}}
        <div class="mt-5 overflow-x-auto rounded-md border border-[#E4E4E7]">
            <table class="w-full min-w-[900px] border-collapse text-sm">
                <thead>
                    <tr class="h-11 bg-[#F2F2F7] text-left font-medium text-[#222]">
                        <th class="px-4 font-medium">Nama Kurikulum</th>
                        <th class="px-4 font-medium">Tahun Terbit</th>
                        <th class="px-4 font-medium">Status</th>
                        <th class="px-4 font-medium">Aksi</th>
                        <th class="px-4 font-medium">Dokumen</th>
                    </tr>
                </thead>
                <tbody id="tbody"></tbody>
            </table>
        </div>

        {{-- Info + paginasi --}}
        <div class="mt-5 flex flex-wrap items-center justify-between gap-3">
            <span id="info" class="text-sm text-[#55555C]"></span>
            <div class="flex gap-2">
                <button type="button" class="size-8 rounded-md border border-[#E4E4E7] bg-white text-[13px] text-[#8A8A90]">‹</button>
                <button type="button" class="size-8 rounded-md bg-[#1F73FF] text-[13px] text-white">1</button>
                <button type="button" class="size-8 rounded-md border border-[#E4E4E7] bg-white text-[13px] text-[#8A8A90]">›</button>
            </div>
        </div>
    </section>
</main>

{{-- ===================== POP-UP TAMBAH / EDIT ===================== --}}
<div id="modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <div class="w-full max-w-[425px] rounded-lg bg-white p-6 shadow-lg">
        <h3 id="modalTitle" class="text-lg font-semibold">Tambah Kurikulum</h3>
        <div class="mt-4 flex flex-col gap-3">
            <input id="fName" placeholder="Nama kurikulum" maxlength="120" class="h-9 w-full rounded-md border border-[#E4E4E7] bg-white px-3 text-sm outline-none focus:border-[#1F73FF]">
            <input id="fDesc" placeholder="Keterangan" maxlength="255" class="h-9 w-full rounded-md border border-[#E4E4E7] bg-white px-3 text-sm outline-none focus:border-[#1F73FF]">
            <input id="fYear" placeholder="Tahun terbit" inputmode="numeric" maxlength="4" class="h-9 w-full rounded-md border border-[#E4E4E7] bg-white px-3 text-sm outline-none focus:border-[#1F73FF]">

            <label class="flex flex-col gap-1">
                <span id="fPdfLabel" class="text-xs font-medium text-[#55555C]">Dokumen PDF (opsional, maks. 20 MB)</span>
                <input id="fPdf" type="file" accept="application/pdf"
                       class="w-full cursor-pointer rounded-md border border-dashed border-[#BDBDBD] bg-[#FAFAFA] p-2 text-xs text-[#55555C] file:mr-3 file:cursor-pointer file:rounded-md file:border-0 file:bg-[#E3EDFF] file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-[#1F73FF]">
            </label>

            <p id="fError" class="hidden text-xs text-[#E5484D]"></p>
        </div>
        <div class="mt-5 flex justify-end gap-2">
            <button id="btnCancel" type="button" class="h-9 rounded-md border border-[#E4E4E7] bg-white px-4 text-sm font-medium">Batal</button>
            <button id="btnSave" type="button" class="h-9 rounded-md bg-[#1F73FF] px-4 text-sm font-medium text-white disabled:opacity-60">Simpan</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var data = @json($kurikulum);
    var URL_BASE = @js(route('admin.kurikulum.trk'));   // /admin/kurikulum/trk
    var CSRF = document.querySelector('meta[name=csrf-token]').content;
    var q = '', statusFilter = 'all', editId = null, busy = false;

    var $ = function (id) { return document.getElementById(id); };
    var tbody = $('tbody'), info = $('info'), modal = $('modal');

    function esc(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }
    function icon(paths) {
        return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.33" stroke-linecap="round" stroke-linejoin="round">' + paths + '</svg>';
    }
    var I_REFRESH = icon('<path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" /><path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />');
    var I_EDIT    = icon('<path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" />');
    var I_TRASH   = icon('<path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1v3" />');
    var I_FILE    = icon('<path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M9 9l1 0" /><path d="M9 13l6 0" /><path d="M9 17l6 0" />');

    /* ---------- kirim ke server ---------- */
    function request(method, url, body) {
        var opts = { method: method, headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } };
        if (body) opts.body = body;
        return fetch(url, opts).then(function (res) {
            return res.json().catch(function () { return {}; }).then(function (json) {
                if (!res.ok) {
                    var msg = json.errors ? Object.values(json.errors)[0][0] : (json.message || 'Terjadi kesalahan, coba lagi.');
                    throw new Error(msg);
                }
                return json;
            });
        });
    }

    /* ---------- tampilkan tabel ---------- */
    function render() {
        var list = data.filter(function (r) {
            return r.name.toLowerCase().indexOf(q.toLowerCase()) !== -1 &&
                   (statusFilter === 'all' || r.status === statusFilter);
        });

        tbody.innerHTML = list.length ? list.map(function (r) {
            var active = r.status === 'aktif';
            return '<tr class="h-[65px] border-t border-[#E4E4E7]">' +
                '<td class="px-4"><div>' + esc(r.name) + '</div><div class="text-xs text-[#8A8A90]">' + esc(r.description) + '</div></td>' +
                '<td class="px-4">' + esc(r.year) + '</td>' +
                '<td class="px-4"><span class="inline-flex h-[26px] items-center gap-1.5 rounded-full px-3 text-[13px] font-medium ' +
                    (active ? 'bg-[#E3F6E8] text-[#17803D]' : 'bg-[#EDEDF0] text-[#55555C]') + '">' +
                    '<span class="size-1.5 rounded-full ' + (active ? 'bg-[#17803D]' : 'bg-[#8A8A90]') + '"></span>' +
                    (active ? 'Aktif' : 'Nonaktif') + '</span></td>' +
                '<td class="px-4"><div class="flex gap-2">' +
                    '<button type="button" title="Ubah status" data-act="toggle" data-id="' + esc(r.id) + '" class="flex size-8 items-center justify-center rounded-md bg-[#FFF1D6] text-[#C77700]">' + I_REFRESH + '</button>' +
                    '<button type="button" title="Edit keterangan" data-act="edit" data-id="' + esc(r.id) + '" class="flex size-8 items-center justify-center rounded-md bg-[#E3EDFF] text-[#1F73FF]">' + I_EDIT + '</button>' +
                    '<button type="button" title="Hapus" data-act="delete" data-id="' + esc(r.id) + '" class="flex size-8 items-center justify-center rounded-md bg-[#FFD9D9] text-[#E5484D]">' + I_TRASH + '</button>' +
                '</div></td>' +
                '<td class="px-4"><button type="button" data-act="pdf" data-id="' + esc(r.id) + '" class="flex h-8 items-center gap-1.5 rounded-md border border-[#1F73FF] bg-white px-3 text-sm font-medium text-[#1F73FF]">' + I_FILE + 'Lihat PDF</button></td>' +
                '</tr>';
        }).join('') :
            '<tr class="h-[65px] border-t border-[#E4E4E7]"><td colspan="5" class="px-4 text-center text-[#8A8A90]">Tidak ada kurikulum.</td></tr>';

        info.textContent = 'Menampilkan ' + (list.length ? 1 : 0) + '–' + list.length + ' dari ' + list.length + ' kurikulum';
    }

    /* ---------- pop-up ---------- */
    function showError(msg) { var e = $('fError'); e.textContent = msg; e.classList.remove('hidden'); }

    function openForm(row) {
        editId = row ? row.id : null;
        $('modalTitle').textContent = row ? 'Edit Kurikulum' : 'Tambah Kurikulum';
        $('fName').value = row ? row.name : '';
        $('fDesc').value = row ? row.description : '';
        $('fYear').value = row ? row.year : '';
        $('fPdf').value = '';
        $('fPdfLabel').textContent = row && row.pdf_url
            ? 'Ganti dokumen PDF (kosongkan jika tidak diganti)'
            : 'Dokumen PDF (opsional, maks. 20 MB)';
        $('fError').classList.add('hidden');
        modal.classList.remove('hidden'); modal.classList.add('flex');
        $('fName').focus();
    }
    function closeForm() { modal.classList.add('hidden'); modal.classList.remove('flex'); }

    function save() {
        if (busy) return;
        var name = $('fName').value.trim(), desc = $('fDesc').value.trim(), year = $('fYear').value.trim();
        if (!name || !year) { showError('Nama kurikulum dan tahun terbit wajib diisi.'); return; }
        if (!/^\d{4}$/.test(year)) { showError('Tahun terbit harus 4 angka, mis. 2026.'); return; }

        var fd = new FormData();
        fd.append('name', name); fd.append('description', desc); fd.append('year', year);
        var file = $('fPdf').files[0];
        if (file) fd.append('pdf', file);
        if (editId) fd.append('_method', 'PUT');

        busy = true; $('btnSave').disabled = true;
        request('POST', editId ? URL_BASE + '/' + encodeURIComponent(editId) : URL_BASE, fd)
            .then(function (json) {
                if (editId) {
                    data = data.map(function (r) { return r.id === editId ? json.data : r; });
                } else {
                    data.unshift(json.data);
                }
                closeForm(); render();
            })
            .catch(function (err) { showError(err.message); })
            .then(function () { busy = false; $('btnSave').disabled = false; });
    }

    /* ---------- event ---------- */
    $('btnAdd').addEventListener('click', function () { openForm(null); });
    $('btnCancel').addEventListener('click', closeForm);
    $('btnSave').addEventListener('click', save);
    modal.addEventListener('click', function (e) { if (e.target === modal) closeForm(); });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeForm();
        if (e.key === 'Enter' && !modal.classList.contains('hidden') && e.target.tagName === 'INPUT' && e.target.type !== 'file') save();
    });
    $('q').addEventListener('input', function (e) { q = e.target.value; render(); });
    $('statusFilter').addEventListener('change', function (e) { statusFilter = e.target.value; render(); });

    tbody.addEventListener('click', function (e) {
        var b = e.target.closest('button[data-act]'); if (!b) return;
        var row = data.filter(function (r) { return r.id === b.dataset.id; })[0]; if (!row) return;
        var url = URL_BASE + '/' + encodeURIComponent(row.id);

        if (b.dataset.act === 'toggle') {
            request('PATCH', url + '/status').then(function (json) {
                data = data.map(function (r) { return r.id === row.id ? json.data : r; });
                render();
            }).catch(function (err) { alert(err.message); });
        } else if (b.dataset.act === 'edit') {
            openForm(row);
        } else if (b.dataset.act === 'delete') {
            if (confirm('Hapus ' + row.name + '?')) {
                request('DELETE', url).then(function () {
                    data = data.filter(function (r) { return r.id !== row.id; });
                    render();
                }).catch(function (err) { alert(err.message); });
            }
        } else if (b.dataset.act === 'pdf') {
            if (row.pdf_url) window.open(row.pdf_url, '_blank', 'noopener');
            else alert('Dokumen PDF belum diunggah. Klik Edit untuk mengunggahnya.');
        }
    });

    render();
})();
</script>
@endpush