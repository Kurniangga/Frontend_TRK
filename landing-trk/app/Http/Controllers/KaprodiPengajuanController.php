<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KaprodiPengajuanController extends Controller
{
    private const FILE = 'kaprodi/pengajuan-keputusan.json';

    private const STATUS = [
        'setujui' => 'Disetujui',
        'revisi'  => 'Direvisi',
        'tolak'   => 'Ditolak',
    ];

    /** Halaman Daftar Pengajuan (Loket Masuk) */
    public function index()
    {
        return view('kaprodi.pengajuan', ['rows' => $this->daftarPengajuan()]);
    }

    /** Halaman Detail / Tinjau Pengajuan */
    public function show(string $id)
    {
        $k = $this->detail($id);
        $keputusan = $this->semuaKeputusan();

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

        return view('kaprodi.tinjau-pengajuan', [
            'id'         => $id,
            'judul'      => $k['judul'],
            'status'     => $status,
            'info'       => $k['info'],
            'anggota'    => $k['anggota'],
            'dokumen'    => $k['dokumen'],
            'riwayat'    => $riwayat,
            'nomorSurat' => $nomorSurat,
            'catatan'    => $catatan,
        ]);
    }

    /** Proses Keputusan Kaprodi */
    public function keputusan(Request $request, string $id)
    {
        $data = $request->validate([
            'aksi'        => ['required', 'in:setujui,revisi,tolak'],
            'catatan'     => ['nullable', 'string', 'max:1000', 'required_if:aksi,revisi,tolak'],
            'nomor_surat' => ['nullable', 'string', 'max:100', 'required_if:aksi,setujui'],
        ], [
            'catatan.required_if'     => 'Catatan revisi wajib diisi',
            'nomor_surat.required_if' => 'Nomor surat wajib diisi saat menyetujui',
        ]);

        $status  = self::STATUS[$data['aksi']];
        $catatan = trim((string) ($data['catatan'] ?? ''));
        $nomor   = trim((string) ($data['nomor_surat'] ?? ''));
        $tanggal = Carbon::now()->locale('id')->translatedFormat('d F Y');

        $baris = "{$status} oleh Kaprodi, {$tanggal}";
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
            ->route('kaprodi.pengajuan.show', $id)
            ->with('success', "Pengajuan berhasil diubah menjadi: {$status}.");
    }

    private function semuaKeputusan(): array
    {
        $disk = Storage::disk('local');
        if (! $disk->exists(self::FILE)) {
            return [];
        }
        return json_decode($disk->get(self::FILE), true) ?: [];
    }

    private function daftarPengajuan(): array
    {
        $kegiatan = [
            ['Seminar Nasional Kecerdasan Buatan', 'Seminar'],
            ['Penelitian Internet of Things',      'Penelitian'],
            ['Pengabdian Pelatihan Web Desa',      'Pengabdian'],
            ['Pelatihan Sertifikasi Jaringan',     'Pelatihan'],
            ['Kompetisi Pengembangan Web',         'Lomba'],
            ['Magang Industri Semester 6',         'Magang'],
        ];
        
        $pengaju = ['Dea Cantika', 'Kuwat Santoso', 'Rizky Pratama', 'Aulia Rahmawati', 'Angga Wahyu Wibowo'];
        $status  = ['Diajukan', 'Direvisi', 'Disetujui', 'Diajukan', 'Direvisi']; 

        $rows = [];
        for ($i = 0; $i < 25; $i++) {
            $k = $kegiatan[$i % 6];
            $rows[] = [
                'id'            => (string) ($i + 1),
                'nama'          => $k[0],
                'kategori'      => $k[1],
                'pengaju'       => $pengaju[$i % 5],
                'tanggal'       => Carbon::create(2026, 1 + ($i % 10), 1 + (($i * 3) % 28))->locale('id')->translatedFormat('j F Y'),
                
                // Ubah angka modulusnya dari 4 menjadi 5 karena isi array $status sekarang ada 5
                'status'        => $status[$i % 5], 
                
                'semester'      => $i % 2 === 0 ? 'Ganjil' : 'Genap',
                'jenis_pengaju' => $i % 2 === 0 ? 'Dosen' : 'Mahasiswa', 
            ];
        }
        return $rows;
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

        $nama = $kegiatan[$i % 6][0];
        $kat  = $kegiatan[$i % 6][1];
        $p    = $pengaju_list[$i % 5];
        $st   = $status_list[$i % 4];
        $tgl  = Carbon::create(2026, 1 + ($i % 10), 1 + (($i * 3) % 28))->locale('id')->translatedFormat('j F Y');

        return [
            'judul'  => $nama,
            'status' => $st,
            'info'   => [
                ['Pengaju', $p],
                ['Kategori', $kat],
                ['Jenis', 'Kegiatan Akademik'],
                ['Penyelenggara', 'Politeknik Negeri Semarang'],
                ['Tanggal', $tgl],
            ],
            'anggota' => [
                [$p, 'Ketua'],
                ['Filda Huwani Dewi', 'Anggota'],
            ],
            'dokumen' => [
                ['proposal_kegiatan.pdf', 'Proposal', '#'],
                ['surat_undangan.pdf', 'Surat Undangan', '#'],
            ],
            'riwayat' => [
                "{$st} oleh {$p}, {$tgl}",
                'Menunggu verifikasi kaprodi',
            ],
        ];
    }
}