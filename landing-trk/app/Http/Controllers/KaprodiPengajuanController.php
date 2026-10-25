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
        $rows = $this->daftarPengajuan();
        $keputusan = $this->semuaKeputusan();

        // Timpa status dummy dengan status asli dari JSON jika sudah diproses
        foreach ($rows as $key => $row) {
            $id = $row['id'];
            if (isset($keputusan[$id])) {
                $rows[$key]['status'] = $keputusan[$id]['status'];
            }
        }

        return view('kaprodi.pengajuan', ['rows' => $rows]);
    }

    /** Halaman Detail / Tinjau Pengajuan */
    public function show(string $id)
    {
        $k = $this->detail($id);
        $keputusan = $this->semuaKeputusan();
        
        // Definisikan variabel $tersimpan
        $tersimpan = $keputusan[$id] ?? null;

        $status  = $k['status'];
        $catatan = null;
        $riwayat = $k['riwayat'];

        // Jika ada keputusan tersimpan, timpa data dummy
        if ($tersimpan) {
            $status  = $tersimpan['status'];
            $catatan = $tersimpan['catatan'] ?? null;
            $riwayat = array_merge(array_slice($riwayat, 0, 1), $tersimpan['riwayat']);
        }

        return view('kaprodi.tinjau-pengajuan', [
            'id'      => $id,
            'judul'   => $k['judul'],
            'status'  => $status,
            'info'    => $k['info'],
            'anggota' => $k['anggota'],
            'dokumen' => $k['dokumen'],
            'riwayat' => $riwayat,
            'catatan' => $catatan,
        ]);
    }

    /** Proses Keputusan Kaprodi */
    public function keputusan(Request $request, string $id)
    {
        // Validasi diubah untuk mendukung upload file 'surat_keputusan'
        $data = $request->validate([
            'aksi'            => ['required', 'in:setujui,revisi,tolak'],
            'catatan'         => ['nullable', 'string', 'max:1000', 'required_if:aksi,revisi,tolak'],
            'surat_keputusan' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:2048', 'required_if:aksi,setujui'],
        ], [
            'catatan.required_if'         => 'Catatan revisi wajib diisi.',
            'surat_keputusan.required_if' => 'File surat keputusan wajib diunggah saat menyetujui.',
            'surat_keputusan.mimes'       => 'Format surat harus PDF, DOC, atau DOCX.',
            'surat_keputusan.max'         => 'Ukuran surat maksimal 2MB.',
        ]);

        $status  = self::STATUS[$data['aksi']];
        $catatan = trim((string) ($data['catatan'] ?? ''));
        $tanggal = Carbon::now()->locale('id')->translatedFormat('d F Y');

        // Simulasi nama file yang diunggah
        $namaFile = '';
        if ($request->hasFile('surat_keputusan')) {
            $namaFile = $request->file('surat_keputusan')->getClientOriginalName();
            // Catatan: Jika menggunakan database asli, simpan file dengan ->storeAs() di sini
        }

        $baris = "{$status} oleh Kaprodi, {$tanggal}";
        if ($data['aksi'] === 'setujui' && $namaFile !== '') {
            $baris .= " (File: {$namaFile})";
        }
        if ($catatan !== '') {
            $baris .= ": {$catatan}";
        }

        $semua = $this->semuaKeputusan();
        $semua[$id] = [
            'status'          => $status,
            'surat_keputusan' => $data['aksi'] === 'setujui' ? $namaFile : null,
            'catatan'         => $catatan !== '' ? $catatan : null,
            'riwayat'         => [$baris],
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
        // Disamakan array-nya agar sinkron dengan daftar tabel
        $status_list  = ['Diajukan', 'Direvisi', 'Disetujui', 'Diajukan', 'Direvisi'];

        $nama = $kegiatan[$i % 6][0];
        $kat  = $kegiatan[$i % 6][1];
        $p    = $pengaju_list[$i % 5];
        $st   = $status_list[$i % 5];
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