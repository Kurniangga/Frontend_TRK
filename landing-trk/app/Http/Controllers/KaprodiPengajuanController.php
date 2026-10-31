<?php

namespace App\Http\Controllers;

use App\Repositories\PengajuanStore;
use App\Repositories\SuratPersetujuanStore;
use App\Repositories\TandaTanganStore;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Pengajuan sisi KAPRODI.
 *
 * Alur: admin menyetujui + memberi nomor surat -> status "Menunggu Tanda Tangan"
 *       -> kaprodi menempelkan tanda tangan tersimpan -> status "Disetujui"
 *       (surat final = snapshot di SuratPersetujuanStore + salinan gambar tanda tangan).
 * Kaprodi juga bisa meminta revisi (kembali ke antrean admin) atau menolak.
 *
 * TODO-DB: semua data lewat PengajuanStore / SuratPersetujuanStore / TandaTanganStore,
 *          ganti isi store-nya saat database siap; controller ini tidak perlu berubah banyak.
 */
class KaprodiPengajuanController extends Controller
{
    private const STATUS = [
        'setujui' => 'Disetujui',
        'revisi'  => 'Direvisi',
        'tolak'   => 'Ditolak',
    ];

    public function __construct(
        private PengajuanStore $pengajuan,
        private SuratPersetujuanStore $surat,
        private TandaTanganStore $ttd,
    ) {
    }

    /** Daftar: semua pengajuan yang pernah dikirim admin ke kaprodi */
    public function index()
    {
        return view('kaprodi.pengajuan', ['rows' => $this->pengajuan->daftarKaprodi()]);
    }

    /** Detail + pratinjau surat + tombol tanda tangan */
    public function show(string $id)
    {
        $p    = $this->untukKaprodi($id);
        $snap = $this->surat->find((string) $p->id);

        $sudahTtd = $p->status === 'Disetujui' && ! empty($snap['tanda_tangan_path']);
        $bisaTtd  = $p->status === PengajuanStore::MENUNGGU_TTD && $snap !== null;
        $ttdAktif = $this->ttd->aktif(TandaTanganStore::kunci()) !== null;

        // Surat final memakai salinan di surat; sebelum itu, pratinjau memakai tanda tangan aktif
        if ($sudahTtd) {
            $ttdUrl = route('kaprodi.pengajuan.ttd', $p->id) . '?v=' . strtotime((string) ($snap['ditandatangani_pada'] ?? 'now'));
        } else {
            $ttdUrl = $ttdAktif ? route('kaprodi.tanda-tangan.gambar') : null;
        }

        [$namaKaprodi, $nipKaprodi] = $this->identitasKaprodi($snap, $sudahTtd);

        return view('kaprodi.tinjau-pengajuan', [
            'id'              => (string) $p->id,
            'judul'           => $p->nama,
            'status'          => $p->status,
            'info'            => $this->pengajuan->info($p),
            'anggota'         => $this->pengajuan->anggota($p),
            'dokumen'         => $this->pengajuan->dokumen($p),
            'riwayat'         => $p->riwayat,
            'catatan'         => $p->catatan_kaprodi,
            'nomorSurat'      => $p->nomor_surat,
            'isiSurat'        => ! empty($snap['isi_surat']) ? nl2br(e($snap['isi_surat'])) : null,
            'tanggalSurat'    => $snap['tanggal_surat'] ?? null,
            'adaLampiran'     => (bool) ($snap['lampiran_peserta'] ?? false),
            'peserta'         => $snap['peserta'] ?? [],
            'lampiranDokumen' => array_column($snap['lampiran_dokumen'] ?? [], 'nama'),
            'namaKaprodi'     => $namaKaprodi,
            'nipKaprodi'      => $nipKaprodi,
            'ttdUrl'          => $ttdUrl,
            'ttdAktif'        => $ttdAktif,
            'sudahTtd'        => $sudahTtd,
            'bisaTtd'         => $bisaTtd,
        ]);
    }

    /** Keputusan kaprodi: setujui (dengan tanda tangan), revisi, atau tolak */
    public function keputusan(Request $request, string $id)
    {
        $p = $this->untukKaprodi($id);

        $data = $request->validate([
            'aksi'    => ['required', 'in:setujui,revisi,tolak'],
            'catatan' => ['nullable', 'string', 'max:1000', 'required_if:aksi,revisi,tolak'],
        ], [
            'catatan.required_if' => 'Catatan wajib diisi saat meminta revisi atau menolak.',
            'catatan.max'         => 'Catatan maksimal 1000 karakter.',
        ]);

        if ($p->status !== PengajuanStore::MENUNGGU_TTD) {
            return back()->withErrors(['aksi' => 'Pengajuan ini tidak sedang menunggu tanda tangan.']);
        }

        $status  = self::STATUS[$data['aksi']];
        $catatan = trim((string) ($data['catatan'] ?? ''));
        $tanggal = Carbon::now()->locale('id')->translatedFormat('d F Y');

        if ($data['aksi'] === 'setujui') {
            $snap = $this->surat->find((string) $p->id);
            if (! $snap) {
                return back()->withErrors(['aksi' => 'Surat belum tersedia. Minta admin menyetujui ulang pengajuan ini.']);
            }

            $salinan = $this->ttd->salinKeSurat(TandaTanganStore::kunci(), (string) $p->id);
            if (! $salinan) {
                return back()->withErrors(['tanda_tangan' => 'Unggah tanda tangan terlebih dahulu.']);
            }

            [$nama, $nip] = $this->identitasKaprodi($snap, false);

            $snap['tanda_tangan_path']   = $salinan;
            $snap['ditandatangani_pada'] = Carbon::now()->toIso8601String();
            $snap['kaprodi']             = ['nama' => $nama, 'nip' => $nip];
            $this->surat->save((string) $p->id, $snap);

            $this->pengajuan->simpanKeputusan($p, [
                'status'          => 'Disetujui',
                'catatan_kaprodi' => null,
            ], "Ditandatangani dan disetujui oleh Kaprodi, {$tanggal}");

            return redirect()
                ->route('kaprodi.pengajuan.show', $p->id)
                ->with('success', 'Surat berhasil ditandatangani dan pengajuan disetujui.');
        }

        // Revisi / tolak: kembali ke admin (catatan tampil di halaman admin)
        $baris = "{$status} oleh Kaprodi, {$tanggal}" . ($catatan !== '' ? ": {$catatan}" : '');

        $this->pengajuan->simpanKeputusan($p, [
            'status'          => $status,
            'catatan'         => $catatan !== '' ? $catatan : null,
            'catatan_kaprodi' => $catatan !== '' ? $catatan : null,
            'nomor_surat'     => null,
        ], $baris);

        return redirect()
            ->route('kaprodi.pengajuan.show', $p->id)
            ->with('success', "Pengajuan berhasil diubah menjadi: {$status}.");
    }

    /** Gambar tanda tangan pada surat yang SUDAH ditandatangani (salinan snapshot) */
    public function ttdSurat(string $id)
    {
        $p    = $this->untukKaprodi($id);
        $snap = $this->surat->find((string) $p->id);
        $path = $snap['tanda_tangan_path'] ?? null;

        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path, null, [
            'Cache-Control'          => 'private, no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /* ---------- bantu ---------- */

    /** Kaprodi hanya boleh membuka pengajuan yang sudah dikirim admin. */
    private function untukKaprodi(string $id): object
    {
        $p = $this->pengajuan->cari($id);
        abort_unless($p->sampai_kaprodi, 404);

        return $p;
    }

    /** Nama + NIP kaprodi: surat yang sudah ditandatangani memakai snapshot, selain itu session login. */
    private function identitasKaprodi(?array $snap, bool $sudahTtd): array
    {
        $dariSnap = [$snap['kaprodi']['nama'] ?? 'Nama Kaprodi', $snap['kaprodi']['nip'] ?? '-'];
        if ($sudahTtd) {
            return $dariSnap;
        }

        $s = session('kaprodi');
        $s = is_array($s) ? $s : [];

        $nip = $s['nip'] ?? null;

        return [
            $s['nama'] ?? $dariSnap[0],
            ($nip && $nip !== '-') ? $nip : $dariSnap[1],
        ];
    }
}