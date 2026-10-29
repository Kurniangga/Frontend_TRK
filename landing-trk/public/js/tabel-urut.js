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
 * Klik header: 1x naik (A->Z, kecil->besar, lama->baru), 2x turun, 3x kembali ke urutan awal.
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
            '.urut-btn[data-arah="asc"] .urut-naik,.urut-btn[data-arah="desc"] .urut-turun{fill:#2277FF}';
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
        var state = { key: null, arah: null, tipe: 'teks' };
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
            b.setAttribute('title', 'Urutkan menurut ' + label);
            b.innerHTML = '<span></span>' + IKON;
            b.firstChild.textContent = label;
            th.textContent = '';
            th.appendChild(b);
            th.setAttribute('aria-sort', 'none');
            tombol.push({ th: th, b: b });
            b.addEventListener('click', function () { klik(b); });
        });

        function tandai() {
            tombol.forEach(function (t) {
                var aktif = t.b.getAttribute('data-key') === state.key && state.arah;
                t.b.setAttribute('data-arah', aktif ? state.arah : '');
                t.th.setAttribute('aria-sort', aktif ? (state.arah === 'asc' ? 'ascending' : 'descending') : 'none');
            });
        }

        function klik(b) {
            var key = b.getAttribute('data-key');
            if (state.key !== key) { state.key = key; state.tipe = b.getAttribute('data-tipe'); state.arah = 'asc'; }
            else if (state.arah === 'asc') { state.arah = 'desc'; }
            else { state.key = null; state.arah = null; }
            tandai();
            onUbah(state);
        }

        function ambil(baris, key) {
            return nilai[key] ? nilai[key](baris) : baris[key];
        }

        return {
            /** Salinan list terurut sesuai state; tanpa sorting aktif = urutan asli. */
            urutkan: function (list) {
                if (!state.key) return list;
                var key = state.key, tipe = state.tipe, kali = state.arah === 'desc' ? -1 : 1;
                return list.map(function (r, i) { return { r: r, i: i, v: ambil(r, key) }; })
                    .sort(function (p, q) {
                        var pk = kosong(p.v), qk = kosong(q.v);
                        if (pk || qk) return pk && qk ? p.i - q.i : (pk ? 1 : -1);   // kosong selalu di bawah
                        return (banding(p.v, q.v, tipe) * kali) || (p.i - q.i);
                    })
                    .map(function (p) { return p.r; });
            },
            /** Kembali ke urutan awal (dipanggil saat tab / cari / filter berubah). */
            reset: function () { state.key = null; state.arah = null; tandai(); },
            state: function () { return { key: state.key, arah: state.arah }; }
        };
    }

    global.TabelUrut = { pasang: pasang, _tanggal: tanggalKeAngka, _banding: banding };
})(window);
