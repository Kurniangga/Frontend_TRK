<?php

namespace App\Http\Controllers;

use App\Repositories\KategoriKegiatanStore;
use App\Repositories\PengajuanStore;
use App\Repositories\SuratPersetujuanStore;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Halaman detail Pengajuan Kegiatan (admin): lihat data + keputusan
 * (Setujui / Minta revisi / Tolak) + nomor surat.
 *
 * PERUBAHAN: data kini dari PengajuanStore (bukan dummy + JSON sendiri).
 * "Setujui" TIDAK lagi langsung berstatus Disetujui. Statusnya "Menunggu Tanda Tangan":
 * surat (snapshot) dibuat dan dikirim ke kaprodi. "Disetujui" baru terjadi setelah kaprodi menandatangani.
 *
 * TODO-DB: nama/NIP kaprodi diambil dari data Kaprodi aktif.
 */
class AdminTinjauPengajuanController extends Controller
{
    private const STATUS = [
        'revisi' => 'Direvisi',
        'tolak'  => 'Ditolak',
    ];

    // TODO-DB: ambil dari data Kaprodi yang sedang aktif
    private const NAMA_KAPRODI = 'Nama Kaprodi';
    private const NIP_KAPRODI  = '198001012010101001';

    // Token sementara agar nomor surat bisa diperbarui live lewat JS
    private const TOKEN_NOMOR = '@@NOMOR_SURAT@@';

    public function __construct(
        private PengajuanStore $pengajuan,
        private SuratPersetujuanStore $surat,
    ) {
    }

    public function show(string $id)
    {
        $p = $this->pengajuan->cari($id);
        $k = $this->susun($p);

        $status     = $p->status;
        $nomorSurat = $p->nomor_surat;
        $catatan    = $p->catatan;
        $riwayat    = $k['riwayat'];

        // Surat yang sudah dikirim ke kaprodi / disetujui memakai snapshot, jadi edit template tidak mengubah surat lama
        $dikunci  = in_array($status, [PengajuanStore::MENUNGGU_TTD, 'Disetujui'], true);
        $snapshot = $dikunci ? $this->surat->find((string) $p->id) : null;

        if ($snapshot && ! empty($snapshot['isi_surat'])) {
            $isiSurat     = nl2br(e($snapshot['isi_surat']));
            $tanggalSurat = $snapshot['tanggal_surat'] ?? null;
            $namaKaprodi  = $snapshot['kaprodi']['nama'] ?? self::NAMA_KAPRODI;
            $nipKaprodi   = $snapshot['kaprodi']['nip'] ?? self::NIP_KAPRODI;
        } else {
            $isiSurat     = $this->buatIsiSurat($k, $nomorSurat);
            $tanggalSurat = null;
            $namaKaprodi  = self::NAMA_KAPRODI;
            $nipKaprodi   = self::NIP_KAPRODI;
        }

        $adaLampiran = $snapshot && array_key_exists('lampiran_peserta', $snapshot)
            ? (bool) $snapshot['lampiran_peserta']
            : $this->pakaiLampiran($k);
        $peserta = $snapshot['peserta'] ?? $k['anggota'];

        // Berkas pengaju yang dicentang admin sebagai lampiran (dari input lama jika validasi gagal, atau dari snapshot)
        $namaLampiran     = array_column($snapshot['lampiran_dokumen'] ?? [], 'nama');
        $lampiranTerpilih = [];
        foreach ($k['dokumen'] as $i => $d) {
            $dipilih = session()->hasOldInput()
                ? in_array($i, array_map('intval', (array) old('lampiran', [])), true)
                : in_array($d[0], $namaLampiran, true);
            if ($dipilih) {
                $lampiranTerpilih[] = $i;
            }
        }

        return view('admin.tinjau-pengajuan', [
            'id'               => (string) $p->id,
            'judul'            => $k['judul'],
            'status'           => $status,
            'info'             => $k['info'],
            'anggota'          => $k['anggota'],
            'dokumen'          => $k['dokumen'],
            'riwayat'          => $riwayat,
            'nomorSurat'       => $nomorSurat,
            'catatan'          => $catatan,
            'isiSurat'         => $isiSurat,
            'tanggalSurat'     => $tanggalSurat,
            'adaLampiran'      => $adaLampiran,
            'peserta'          => $peserta,
            'lampiranTerpilih' => $lampiranTerpilih,
            'namaKaprodi'      => $namaKaprodi,
            'nipKaprodi'       => $nipKaprodi,
        ]);
    }

    public function keputusan(Request $request, string $id)
    {
        $data = $request->validate([
            'aksi'        => ['required', 'in:setujui,revisi,tolak'],
            'catatan'     => ['nullable', 'string', 'max:1000', 'required_if:aksi,revisi,tolak'],
            'nomor_surat' => ['nullable', 'string', 'max:100', 'required_if:aksi,setujui'],
            'lampiran'    => ['nullable', 'array'],
            'lampiran.*'  => ['integer', 'min:0'],
        ], [
            'catatan.required_if'     => 'Catatan revisi wajib diisi',
            'nomor_surat.required_if' => 'Nomor surat wajib diisi saat menyetujui',
            'catatan.max'             => 'Catatan maksimal 1000 karakter',
            'nomor_surat.max'         => 'Nomor surat maksimal 100 karakter',
        ]);

        $p = $this->pengajuan->cari($id);

        if ($p->status === 'Disetujui') {
            // View admin menampilkan error di bawah kolom catatan
            return back()->withInput()->withErrors(['catatan' => 'Surat sudah ditandatangani Kaprodi, keputusan tidak dapat diubah.']);
        }

        $catatan = trim((string) ($data['catatan'] ?? ''));
        $nomor   = trim((string) ($data['nomor_surat'] ?? ''));
        $tanggal = Carbon::now()->locale('id')->translatedFormat('d F Y');

        if ($data['aksi'] === 'setujui') {
            $this->simpanSnapshot($p, $nomor, $this->pilihLampiran($p, $data['lampiran'] ?? []));

            $this->pengajuan->simpanKeputusan($p, [
                'status'          => PengajuanStore::MENUNGGU_TTD,
                'nomor_surat'     => $nomor,
                'catatan'         => null,
                'catatan_kaprodi' => null,
                'sampai_kaprodi'  => true,
            ], [
                "Disetujui admin dan dikirim ke Kaprodi, {$tanggal} (No. surat: {$nomor})",
                'Menunggu tanda tangan Kaprodi',
            ]);

            return redirect()
                ->route('admin.pengajuan.show', $id)
                ->with('success', 'Pengajuan disetujui dan suratnya dikirim ke Kaprodi untuk ditandatangani.');
        }

        $status = self::STATUS[$data['aksi']];
        $baris  = "{$status} oleh Admin, {$tanggal}" . ($catatan !== '' ? ": {$catatan}" : '');

        $this->pengajuan->simpanKeputusan($p, [
            'status'      => $status,
            'nomor_surat' => null,
            'catatan'     => $catatan !== '' ? $catatan : null,
        ], $baris);

        $this->surat->delete((string) $p->id);

        return redirect()
            ->route('admin.pengajuan.show', $id)
            ->with('success', "Pengajuan berhasil diubah menjadi: {$status}.");
    }

    /** Bentuk data lama ($k) dari satu pengajuan store, supaya method-method di bawah tidak perlu diubah. */
    private function susun(object $p): array
    {
        return [
            'judul'   => $p->nama,
            'status'  => $p->status,
            'info'    => $this->pengajuan->info($p),
            'anggota' => $this->pengajuan->anggota($p),
            'dokumen' => $this->pengajuan->dokumen($p),
            'riwayat' => $p->riwayat,
        ];
    }

    /**
     * Render template surat balasan sesuai kategori kegiatan (HTML aman, untuk tampilan live).
     * Variabel: [NAMA_PENGAJU] [IDENTITAS] [NAMA_KEGIATAN] [TANGGAL] [LOKASI]
     *           [NOMOR_SURAT] [TANGGAL_SURAT]
     */
    private function buatIsiSurat(array $k, ?string $nomorSurat): string
    {
        $template = $this->cariTemplate($k);

        if ($template === null) {
            return e('Kategori ini tidak memiliki template surat balasan.');
        }

        // Escape dulu (aman XSS), lalu ganti token nomor dengan <span> yang bisa diupdate JS
        $isi = nl2br(e($this->isiTeks($template, $k, self::TOKEN_NOMOR)));

        $nomor  = trim((string) $nomorSurat);
        $tampil = $nomor !== '' ? e($nomor) : '[nomor surat belum diisi]';

        return str_replace(
            self::TOKEN_NOMOR,
            '<span class="js-nomor-surat">' . $tampil . '</span>',
            $isi
        );
    }

    /** Cari kategori berdasarkan nama, prioritaskan yang jenisnya juga cocok. */
    private function cariKategori(array $k, bool $perluTemplate = false): ?array
    {
        $infoMap  = collect($k['info'])->mapWithKeys(fn ($r) => [$r[0] => $r[1]]);
        $kategori = (string) ($infoMap['Kategori'] ?? '');
        $jenis    = (string) ($infoMap['Jenis'] ?? '');

        $kandidat = null;

        foreach ((new KategoriKegiatanStore())->all() as $kat) {
            if (strcasecmp($kat['name'], $kategori) !== 0) {
                continue;
            }
            if ($perluTemplate && empty($kat['template_surat'])) {
                continue;
            }
            if ($jenis !== '' && strcasecmp($kat['jenis'] ?? '', $jenis) === 0) {
                return $kat;
            }
            $kandidat ??= $kat;
        }

        return $kandidat;
    }

    private function cariTemplate(array $k): ?string
    {
        return $this->cariKategori($k, true)['template_surat'] ?? null;
    }

    /** Apakah kategori ini menyertakan lampiran daftar peserta. */
    private function pakaiLampiran(array $k): bool
    {
        return (bool) ($this->cariKategori($k)['lampiran_peserta'] ?? false);
    }

    /** Ganti semua variabel pada template menjadi teks biasa (belum di-escape). */
    private function isiTeks(string $template, array $k, string $nomor, ?string $tanggalSurat = null): string
    {
        $infoMap = collect($k['info'])->mapWithKeys(fn ($r) => [$r[0] => $r[1]]);

        $pemetaan = [
            '[NAMA_PENGAJU]'  => $infoMap['Pengaju'] ?? '-',
            '[IDENTITAS]'     => $infoMap['NIM/NIP'] ?? '-',
            '[NAMA_KEGIATAN]' => $k['judul'],
            '[TANGGAL]'       => $infoMap['Tanggal'] ?? '-',
            '[LOKASI]'        => $infoMap['Lokasi'] ?? ($infoMap['Penyelenggara'] ?? '-'),
            '[TANGGAL_SURAT]' => $tanggalSurat ?? Carbon::now()->locale('id')->translatedFormat('j F Y'),
            '[NOMOR_SURAT]'   => $nomor,
        ];

        return str_replace(array_keys($pemetaan), array_values($pemetaan), $template);
    }

    /** Simpan hasil render final surat ke JSON saat admin menyetujui (belum bertanda tangan). */
    private function simpanSnapshot(object $p, string $nomor, array $lampiranDokumen = []): void
    {
        $k            = $this->susun($p);
        $infoMap      = collect($k['info'])->mapWithKeys(fn ($r) => [$r[0] => $r[1]]);
        $template     = $this->cariTemplate($k);
        $tanggalSurat = Carbon::now()->locale('id')->translatedFormat('j F Y');

        $this->surat->save((string) $p->id, [
            'pengajuan_id'     => (string) $p->id,
            'judul'            => $k['judul'],
            'kategori'         => $infoMap['Kategori'] ?? null,
            'pengaju'          => $infoMap['Pengaju'] ?? null,
            'nomor_surat'      => $nomor,
            'tanggal_surat'    => $tanggalSurat,
            'template_surat'   => $template,
            'isi_surat'        => $template !== null ? $this->isiTeks($template, $k, $nomor, $tanggalSurat) : null,
            'lampiran_peserta' => $this->pakaiLampiran($k),
            'lampiran_dokumen' => $lampiranDokumen,
            'peserta'          => $k['anggota'],
            'kaprodi'          => ['nama' => self::NAMA_KAPRODI, 'nip' => self::NIP_KAPRODI],
            // Diisi KaprodiPengajuanController saat kaprodi menandatangani:
            'tanda_tangan_path'   => null,
            'ditandatangani_pada' => null,
            'disetujui_pada'      => Carbon::now()->toIso8601String(), // = dikirim ke kaprodi
        ]);
    }

    /**
     * Ambil berkas pengaju yang dicentang admin sebagai lampiran (indeks di luar daftar diabaikan).
     * TODO-DB: ganti indeks dengan id_dokumen, lalu simpan ke tabel bukti_history
     *          (dokumen <-> baris timeline berstatus "diterima").
     */
    private function pilihLampiran(object $p, array $indeks): array
    {
        $dokumen = $this->pengajuan->dokumen($p);
        $hasil   = [];

        foreach (array_unique(array_map('intval', $indeks)) as $i) {
            if (isset($dokumen[$i])) {
                $hasil[] = ['nama' => $dokumen[$i][0], 'kategori' => $dokumen[$i][1]];
            }
        }

        return $hasil;
    }
}