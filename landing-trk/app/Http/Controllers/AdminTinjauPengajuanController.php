<?php

namespace App\Http\Controllers;

use App\Repositories\KategoriKegiatanStore;
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
            'isiSurat'   => $this->buatIsiSurat($k, $nomorSurat),
            'namaKaprodi' => self::NAMA_KAPRODI,
            'nipKaprodi'  => self::NIP_KAPRODI,
        ]);
    }

    public function keputusan(Request $request, string $id)
    {
        $data = $request->validate([
            'aksi'        => ['required', 'in:setujui,revisi,tolak'],
            'catatan'     => ['nullable', 'string', 'max:1000', 'required_if:aksi,revisi,tolak'],
            'nomor_surat' => ['nullable', 'string', 'max:100', 'required_if:aksi,setujui'],
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

        return redirect()
            ->route('admin.pengajuan.show', $id)
            ->with('success', "Pengajuan berhasil diubah menjadi: {$status}.");
    }

    /**
     * Render template surat balasan sesuai kategori kegiatan.
     * Variabel: [NAMA_PENGAJU] [IDENTITAS] [NAMA_KEGIATAN] [TANGGAL] [LOKASI]
     *           [NOMOR_SURAT] [TANGGAL_SURAT]
     */
    private function buatIsiSurat(array $k, ?string $nomorSurat): string
    {
        $infoMap = collect($k['info'])->mapWithKeys(fn ($r) => [$r[0] => $r[1]]);

        $kategori = (string) ($infoMap['Kategori'] ?? '');
        $jenis    = (string) ($infoMap['Jenis'] ?? '');

        $template = null;
        $kandidat = null;

        foreach ((new KategoriKegiatanStore())->all() as $kat) {
            if (empty($kat['template_surat'])) {
                continue;
            }
            if (strcasecmp($kat['name'], $kategori) !== 0) {
                continue;
            }
            // Prioritaskan yang jenisnya juga cocok; kalau tidak ada, pakai nama saja
            if ($jenis !== '' && strcasecmp($kat['jenis'] ?? '', $jenis) === 0) {
                $template = $kat['template_surat'];
                break;
            }
            $kandidat ??= $kat['template_surat'];
        }
        $template ??= $kandidat;

        if ($template === null) {
            return e('Kategori ini tidak memiliki template surat balasan.');
        }

        $pemetaan = [
            '[NAMA_PENGAJU]'  => $infoMap['Pengaju'] ?? '-',
            '[IDENTITAS]'     => $infoMap['NIM/NIP'] ?? '-',
            '[NAMA_KEGIATAN]' => $k['judul'],
            '[TANGGAL]'       => $infoMap['Tanggal'] ?? '-',
            '[LOKASI]'        => $infoMap['Lokasi'] ?? ($infoMap['Penyelenggara'] ?? '-'),
            '[TANGGAL_SURAT]' => Carbon::now()->locale('id')->translatedFormat('j F Y'),
            '[NOMOR_SURAT]'   => self::TOKEN_NOMOR,
        ];

        $isi = str_replace(array_keys($pemetaan), array_values($pemetaan), $template);

        // Escape dulu (aman XSS), lalu ganti token nomor dengan <span> yang bisa diupdate JS
        $isi = nl2br(e($isi));

        $nomor  = trim((string) $nomorSurat);
        $tampil = $nomor !== '' ? e($nomor) : '[nomor surat belum diisi]';

        return str_replace(
            self::TOKEN_NOMOR,
            '<span class="js-nomor-surat">' . $tampil . '</span>',
            $isi
        );
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