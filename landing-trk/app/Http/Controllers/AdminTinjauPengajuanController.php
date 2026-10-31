<?php

namespace App\Http\Controllers;

use App\Repositories\KategoriKegiatanStore;
use App\Repositories\SuratPersetujuanStore;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Halaman detail Pengajuan Kegiatan (admin): lihat data pengajuan + keputusan
 * (Setujui / Minta revisi / Tolak) + nomor surat.
 *
 * TODO-DB: data pengajuan masih dummy (detail()), keputusan disimpan ke file JSON.
 * Saat database siap, ganti detail() dengan query dan keputusan() dengan update tabel.
 */
class AdminTinjauPengajuanController extends Controller
{
    private const FILE = 'admin/pengajuan-keputusan.json';

    private const STATUS = [
        'setujui' => 'Disetujui',
        'revisi'  => 'Direvisi',
        'tolak'   => 'Ditolak',
    ];

    // TODO-DB: ambil dari data Kaprodi yang sedang aktif
    private const NAMA_KAPRODI = 'Nama Kaprodi';
    private const NIP_KAPRODI  = '198001012010101001';

    // Token sementara agar nomor surat bisa diperbarui live lewat JS
    private const TOKEN_NOMOR = '@@NOMOR_SURAT@@';

    public function __construct(private SuratPersetujuanStore $surat)
    {
    }

    public function show(string $id)
    {
        $k = $this->detail($id);
        $tersimpan = $this->semuaKeputusan()[$id] ?? null;

        $status     = $k['status'];
        $nomorSurat = null;
        $catatan    = null;
        $riwayat    = $k['riwayat'];

        if ($tersimpan) {
            $status     = $tersimpan['status'];
            $nomorSurat = $tersimpan['nomor_surat'] ?? null;
            $catatan    = $tersimpan['catatan'] ?? null;
            $riwayat    = array_merge(array_slice($riwayat, 0, 1), $tersimpan['riwayat']);
        }

        // Surat yang sudah disetujui memakai snapshot, jadi edit template tidak mengubah surat lama
        $snapshot = $status === 'Disetujui' ? $this->surat->find($id) : null;
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
            'id'         => $id,
            'judul'      => $k['judul'],
            'status'     => $status,
            'info'       => $k['info'],
            'anggota'    => $k['anggota'],
            'dokumen'    => $k['dokumen'],
            'riwayat'    => $riwayat,
            'nomorSurat' => $nomorSurat,
            'catatan'    => $catatan,
            'isiSurat'   => $isiSurat,
            'tanggalSurat' => $tanggalSurat,
            'adaLampiran'  => $adaLampiran,
            'peserta'      => $peserta,
            'lampiranTerpilih' => $lampiranTerpilih,
            'namaKaprodi' => $namaKaprodi,
            'nipKaprodi'  => $nipKaprodi,
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

        $status  = self::STATUS[$data['aksi']];
        $catatan = trim((string) ($data['catatan'] ?? ''));
        $nomor   = trim((string) ($data['nomor_surat'] ?? ''));
        $tanggal = Carbon::now()->locale('id')->translatedFormat('d F Y');

        $baris = "{$status} oleh Admin, {$tanggal}";
        if ($data['aksi'] === 'setujui' && $nomor !== '') {
            $baris .= " (No. surat: {$nomor})";
        }
        if ($catatan !== '') {
            $baris .= ": {$catatan}";
        }

        $semua = $this->semuaKeputusan();
        $semua[$id] = [
            'status'      => $status,
            'nomor_surat' => $data['aksi'] === 'setujui' ? $nomor : null,
            'catatan'     => $catatan !== '' ? $catatan : null,
            'riwayat'     => [$baris],
        ];
        Storage::disk('local')->put(self::FILE, json_encode($semua, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        if ($data['aksi'] === 'setujui') {
            $this->simpanSnapshot($id, $nomor, $this->pilihLampiran($id, $data['lampiran'] ?? []));
        } else {
            $this->surat->delete($id);
        }

        return redirect()
            ->route('admin.pengajuan.show', $id)
            ->with('success', "Pengajuan berhasil diubah menjadi: {$status}.");
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

    /** Simpan hasil render final surat ke JSON saat pengajuan disetujui. */
    private function simpanSnapshot(string $id, string $nomor, array $lampiranDokumen = []): void
    {
        $k            = $this->detail($id);
        $infoMap      = collect($k['info'])->mapWithKeys(fn ($r) => [$r[0] => $r[1]]);
        $template     = $this->cariTemplate($k);
        $tanggalSurat = Carbon::now()->locale('id')->translatedFormat('j F Y');

        $this->surat->save($id, [
            'pengajuan_id'   => $id,
            'judul'          => $k['judul'],
            'kategori'       => $infoMap['Kategori'] ?? null,
            'pengaju'        => $infoMap['Pengaju'] ?? null,
            'nomor_surat'    => $nomor,
            'tanggal_surat'  => $tanggalSurat,
            'template_surat' => $template,
            'isi_surat'      => $template !== null ? $this->isiTeks($template, $k, $nomor, $tanggalSurat) : null,
            'lampiran_peserta' => $this->pakaiLampiran($k),
            'lampiran_dokumen' => $lampiranDokumen,
            'peserta'        => $k['anggota'],
            'kaprodi'        => ['nama' => self::NAMA_KAPRODI, 'nip' => self::NIP_KAPRODI],
            'disetujui_pada' => Carbon::now()->toIso8601String(),
        ]);
    }

    /**
     * Ambil berkas pengaju yang dicentang admin sebagai lampiran (indeks di luar daftar diabaikan).
     * TODO-DB: ganti indeks dengan id_dokumen, lalu simpan ke tabel bukti_history
     *          (dokumen <-> baris timeline berstatus "diterima").
     */
    private function pilihLampiran(string $id, array $indeks): array
    {
        $dokumen = $this->detail($id)['dokumen'];
        $hasil   = [];

        foreach (array_unique(array_map('intval', $indeks)) as $i) {
            if (isset($dokumen[$i])) {
                $hasil[] = ['nama' => $dokumen[$i][0], 'kategori' => $dokumen[$i][1]];
            }
        }

        return $hasil;
    }

    private function semuaKeputusan(): array
    {
        $disk = Storage::disk('local');
        if (! $disk->exists(self::FILE)) {
            return [];
        }
        return json_decode($disk->get(self::FILE), true) ?: [];
    }

    private function detail(string $id): array
    {
        $i = max(0, intval($id) - 1);

        $kegiatan = [
            ['Seminar Nasional Kecerdasan Buatan', 'Seminar'],
            ['Penelitian Internet of Things',      'Penelitian'],
            ['Pengabdian Pelatihan Web Desa',      'Pengabdian'],
            ['Pelatihan Sertifikasi Jaringan',     'Pelatihan'],
            ['Kompetisi Pengembangan Web',         'Lomba'],
            ['Magang Industri Semester 6',         'Magang'],
        ];

        $pengaju_list = ['Dea Cantika', 'Kuwat Santoso', 'Rizky Pratama', 'Aulia Rahmawati', 'Angga Wahyu Wibowo'];
        $status_list  = ['Diajukan', 'Direvisi', 'Diajukan', 'Direvisi'];

        $nama_kegiatan = $kegiatan[$i % 6][0];
        $kategori      = $kegiatan[$i % 6][1];
        $pengaju       = $pengaju_list[$i % 5];
        $status        = $status_list[$i % 4];
        $tanggal       = Carbon::create(2026, 1 + ($i % 10), 1 + (($i * 3) % 28))->locale('id')->translatedFormat('j F Y');

        return [
            'judul'  => $nama_kegiatan,
            'status' => $status,
            'info'   => [
                ['Pengaju', $pengaju],
                ['NIM/NIP', '3.34.23.' . str_pad((string) (($i % 5) + 1), 4, '0', STR_PAD_LEFT)], // dummy
                ['Kategori', $kategori],
                ['Jenis', 'Kegiatan Akademik'],
                ['Penyelenggara', 'Politeknik Negeri Semarang'],
                ['Lokasi', 'Politeknik Negeri Semarang'],
                ['Tanggal', $tanggal],
            ],
            'anggota' => [
                [$pengaju, 'Ketua'],
                ['Filda Huwani Dewi', 'Anggota'],
            ],
            'dokumen' => [
                ['proposal_kegiatan.pdf', 'Proposal', '#'],
                ['surat_undangan.pdf', 'Surat Undangan', '#'],
            ],
            'riwayat' => [
                "{$status} oleh {$pengaju}, {$tanggal}",
                'Menunggu verifikasi admin/kaprodi',
            ],
        ];
    }
}