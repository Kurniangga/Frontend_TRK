/*
 * public/js/tabel-urut.js
 * Sorting kolom untuk tabel daftar (admin & kaprodi). Tanpa library, tanpa build (tidak lewat Vite).
 *
 * PEMAKAIAN (di halaman daftar):
 *   1. Beri atribut pada <th> kolom yang boleh diurutkan:   <th data-sort="nama">Nama</th>
 *      (opsional data-tipe="teks|angka|tanggal"; bawaan "teks"). <th> tanpa data-sort = tidak bisa diurutkan.
 *   2. Muat file ini, lalu:
 *        var urut = TabelUrut.pasang(document.querySelector('table'), {
 *            nilai: { pangkat: function (r) { return r.jabatan + ' ' + r.golongan; } },  // opsional: nilai urut khusus
 *            onUbah: function () { page = 1; render(); }                                  // dipanggil tiap klik header
 *        });
 *   3. Di filtered():  return urut.urutkan(list);        // mengembalikan salinan terurut
 *      Saat tab / cari / filter berubah:  urut.reset();   // kembali ke urutan awal (tanpa memanggil onUbah)
 *
 * Klik header: 1x naik (A->Z, kecil->besar, lama->baru), 2x turun, 3x kolom itu dilepas dari urutan.
 * BANYAK KOLOM SEKALIGUS: klik kolom lain untuk menambah urutan; angka kecil di samping panah = prioritas
 * (1 = diurutkan dulu, kolom berikutnya memecah nilai yang sama). Tombol "Reset" di kolom Aksi mengosongkan semuanya.
 * Nilai kosong ('', '-', null) selalu di paling bawah, apa pun arahnya.
 *
 * TODO-DB: bila daftar nanti dipaginasi di server, ganti isi onUbah menjadi
 *   window.location = '?urut=' + key + '&arah=' + dir  dan urutkan di controller (orderBy dengan daftar kolom yang diizinkan).
 *   Atribut data-sort di <th> tidak perlu diubah.
 */
(function (global) {
    'use strict';

    var BULAN = { jan: 1, feb: 2, mar: 3, apr: 4, mei: 5, may: 5, jun: 6, jul: 7, agu: 8, aug: 8, ags: 8, sep: 9, okt: 10, oct: 10, nov: 11, des: 12, dec: 12 };
    var COLLATOR = (typeof Intl !== 'undefined' && Intl.Collator) ? new Intl.Collator('id', { numeric: true, sensitivity: 'base' }) : null;

    function css() {
        if (document.getElementById('tabel-urut-css')) return;
        var st = document.createElement('style');
        st.id = 'tabel-urut-css';
        st.textContent =
            '.urut-btn{display:inline-flex;align-items:center;gap:6px;max-width:100%;padding:0;margin:0;border:0;background:none;font:inherit;color:inherit;text-align:left;cursor:pointer}' +
            '.urut-btn:hover{color:#2277FF}' +
            '.urut-btn:focus-visible{outline:2px solid #2277FF;outline-offset:2px;border-radius:4px}' +
            '.urut-ikon{flex:none;width:10px;height:14px}' +
            '.urut-ikon path{fill:#B8B8C0}' +
            '.urut-btn[data-arah="asc"] .urut-naik,.urut-btn[data-arah="desc"] .urut-turun{fill:#2277FF}' +
            '.urut-no{display:none;min-width:16px;height:16px;padding:0 4px;border-radius:8px;background:#2277FF;color:#fff;font-size:10px;font-weight:600;line-height:16px;text-align:center}' +
            '.urut-btn[data-arah="asc"] .urut-no,.urut-btn[data-arah="desc"] .urut-no{display:inline-block}' +
            '.urut-btn[data-arah="asc"],.urut-btn[data-arah="desc"]{color:#2277FF}' +
            '.urut-reset{display:none;align-items:center;gap:4px;padding:2px 8px;border:1px solid #D9D9E0;border-radius:6px;background:#fff;color:#55555C;font:inherit;font-size:12px;font-weight:400;cursor:pointer}' +
            '.urut-reset:hover{border-color:#2277FF;color:#2277FF}';
        document.head.appendChild(st);
    }

    function kosong(v) { return v === null || v === undefined || v === '' || v === '-' || v === '—'; }

    /** "12 Oktober 2026" | "12 Okt 2026" | "2026-10-12" | "12/10/2026" -> angka yyyymmdd (NaN bila gagal). */
    function tanggalKeAngka(v) {
        if (typeof v === 'number') return v;
        var s = String(v).trim().toLowerCase(), m;
        if ((m = s.match(/^(\d{4})-(\d{1,2})-(\d{1,2})/))) return +m[1] * 10000 + +m[2] * 100 + +m[3];
        if ((m = s.match(/^(\d{1,2})[\/-](\d{1,2})[\/-](\d{4})/))) return +m[3] * 10000 + +m[2] * 100 + +m[1];
        if ((m = s.match(/(\d{1,2})\s+([a-z]+)\.?\s+(\d{4})/)) && BULAN[m[2].slice(0, 3)]) return +m[3] * 10000 + BULAN[m[2].slice(0, 3)] * 100 + +m[1];
        var t = Date.parse(v);
        return isNaN(t) ? NaN : t;
    }

    function banding(a, b, tipe) {
        if (tipe === 'angka') {
            return (parseFloat(String(a).replace(/[^\d.,-]/g, '').replace(',', '.')) || 0) - (parseFloat(String(b).replace(/[^\d.,-]/g, '').replace(',', '.')) || 0);
        }
        if (tipe === 'tanggal') {
            var x = tanggalKeAngka(a), y = tanggalKeAngka(b);
            if (!isNaN(x) && !isNaN(y)) return x - y;
        }
        a = String(a); b = String(b);
        return COLLATOR ? COLLATOR.compare(a, b) : (a < b ? -1 : a > b ? 1 : 0);
    }

    function pasang(tabel, opsi) {
        opsi = opsi || {};
        var nilai = opsi.nilai || {};
        var onUbah = opsi.onUbah || function () {};
        var urutan = [];   // [{ key, tipe, arah }] — indeks 0 = prioritas utama
        var resetBtn = null;
        var tombol = [];

        css();

        var IKON = '<svg class="urut-ikon" viewBox="0 0 10 14" aria-hidden="true"><path class="urut-naik" d="M5 0L10 5.5H0z"/><path class="urut-turun" d="M5 14L0 8.5H10z"/></svg>';

        Array.prototype.forEach.call(tabel.querySelectorAll('thead th[data-sort]'), function (th) {
            var label = th.textContent.trim();
            var b = document.createElement('button');
            b.type = 'button';
            b.className = 'urut-btn';
            b.setAttribute('data-key', th.getAttribute('data-sort'));
            b.setAttribute('data-tipe', th.getAttribute('data-tipe') || 'teks');
            b.setAttribute('title', 'Urutkan menurut ' + label + '. Klik kolom lain untuk menambah urutan.');
            b.innerHTML = '<span></span>' + IKON + '<span class="urut-no"></span>';
            b.firstChild.textContent = label;
            th.textContent = '';
            th.appendChild(b);
            th.setAttribute('aria-sort', 'none');
            tombol.push({ th: th, b: b });
            b.addEventListener('click', function () { klik(b); });
        });

        // Tombol Reset diletakkan di kolom Aksi (kolom terakhir yang tidak bisa diurutkan), tampil hanya saat ada urutan aktif.
        var ths = tabel.querySelectorAll('thead th');
        var thAksi = ths.length && !ths[ths.length - 1].hasAttribute('data-sort') ? ths[ths.length - 1] : null;
        if (thAksi) {
            resetBtn = document.createElement('button');
            resetBtn.type = 'button';
            resetBtn.className = 'urut-reset';
            resetBtn.setAttribute('title', 'Kembalikan ke urutan awal');
            resetBtn.textContent = '\u00D7 Reset urutan';
            resetBtn.addEventListener('click', function () { urutan = []; tandai(); onUbah(urutan.slice()); });
            thAksi.appendChild(resetBtn);
        }

        function tandai() {
            tombol.forEach(function (t) {
                var key = t.b.getAttribute('data-key'), idx = -1;
                urutan.forEach(function (u, i) { if (u.key === key) idx = i; });
                t.b.setAttribute('data-arah', idx >= 0 ? urutan[idx].arah : '');
                t.b.querySelector('.urut-no').textContent = idx >= 0 && urutan.length > 1 ? String(idx + 1) : '';
                t.th.setAttribute('aria-sort', idx < 0 ? 'none' : (urutan[idx].arah === 'asc' ? 'ascending' : 'descending'));
            });
            if (resetBtn) resetBtn.style.display = urutan.length ? 'inline-flex' : 'none';
        }

        // Siklus per kolom: belum ada -> naik -> turun -> dilepas. Kolom baru ditambahkan di PRIORITAS TERENDAH.
        function klik(b) {
            var key = b.getAttribute('data-key'), idx = -1;
            urutan.forEach(function (u, i) { if (u.key === key) idx = i; });
            if (idx < 0) urutan.push({ key: key, tipe: b.getAttribute('data-tipe'), arah: 'asc' });
            else if (urutan[idx].arah === 'asc') urutan[idx].arah = 'desc';
            else urutan.splice(idx, 1);
            tandai();
            onUbah(urutan.slice());
        }

        tandai();

        function ambil(baris, key) {
            return nilai[key] ? nilai[key](baris) : baris[key];
        }

        return {
            /** Salinan list terurut sesuai urutan aktif (banyak kolom); tanpa urutan = urutan asli. */
            urutkan: function (list) {
                if (!urutan.length) return list;
                var kriteria = urutan.slice();
                return list.map(function (r, i) {
                        return { r: r, i: i, v: kriteria.map(function (k) { return ambil(r, k.key); }) };
                    })
                    .sort(function (p, q) {
                        for (var c = 0; c < kriteria.length; c++) {
                            var pk = kosong(p.v[c]), qk = kosong(q.v[c]);
                            if (pk && qk) continue;
                            if (pk || qk) return pk ? 1 : -1;               // kosong selalu di bawah
                            var h = banding(p.v[c], q.v[c], kriteria[c].tipe) * (kriteria[c].arah === 'desc' ? -1 : 1);
                            if (h) return h;
                        }
                        return p.i - q.i;                                   // sama persis = urutan asli (stabil)
                    })
                    .map(function (p) { return p.r; });
            },
            /** Kosongkan semua urutan (dipanggil saat tab / cari / filter berubah); tidak memanggil onUbah. */
            reset: function () { urutan = []; tandai(); },
            state: function () { return urutan.map(function (u) { return { key: u.key, arah: u.arah }; }); }
        };
    }

    global.TabelUrut = { pasang: pasang, _tanggal: tanggalKeAngka, _banding: banding };
})(window);