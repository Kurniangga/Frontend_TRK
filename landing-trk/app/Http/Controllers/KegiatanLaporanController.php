<?php

namespace App\Http\Controllers;

use App\Repositories\KegiatanData;
use App\Support\Laporan\LaporanKegiatan;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Unduh laporan data kegiatan dosen / mahasiswa (PDF atau Excel) per kurun waktu tahun.
 * Dipakai admin (/admin/kegiatan/{dosen|mahasiswa}/laporan) DAN kaprodi (/kaprodi/...).
 *   - kaprodi : selalu hanya kegiatan yang sudah disetujui.
 *   - admin   : boleh memilih status (bawaan: Disetujui).
 *
 * Parameter GET: dari (tahun), sampai (tahun), format (pdf | xlsx), status (khusus admin).
 */
class KegiatanLaporanController extends Controller
{
    public function __construct(private KegiatanData $data) {}

    public function dosen(Request $request): Response
    {
        return $this->unduh($request, 'dosen');
    }

    public function mahasiswa(Request $request): Response
    {
        return $this->unduh($request, 'mahasiswa');
    }

    private function unduh(Request $request, string $jenis): Response
    {
        $kaprodi = $request->routeIs('kaprodi.*');
        $rows = $jenis === 'dosen' ? $this->data->dosen() : $this->data->mahasiswa();

        // Tahun: bila kosong/tidak valid pakai tahun terbaru; bila terbalik ditukar.
        $tahunList = $this->data->tahun($rows);
        $terbaru = end($tahunList) ?: (int) date('Y');
        $dari = (int) $request->query('dari', $terbaru);
        $sampai = (int) $request->query('sampai', $terbaru);
        $dari = in_array($dari, $tahunList, true) ? $dari : $terbaru;
        $sampai = in_array($sampai, $tahunList, true) ? $sampai : $terbaru;
        if ($dari > $sampai) {
            [$dari, $sampai] = [$sampai, $dari];
        }

        // Status: kaprodi dikunci "Disetujui".
        $status = KegiatanData::DISETUJUI;
        if (! $kaprodi && in_array($request->query('status'), ['Semua', ...KegiatanData::STATUS], true)) {
            $status = $request->query('status');
        }

        $format = $request->query('format') === 'xlsx' ? 'xlsx' : 'pdf';

        $laporan = LaporanKegiatan::susun($jenis, $rows, $dari, $sampai, $status);
        $oleh = (string) session($kaprodi ? 'kaprodi.nama' : 'admin.nama', $kaprodi ? 'Kaprodi' : 'Admin');
        $waktu = now()->setTimezone('Asia/Jakarta')->locale('id')->translatedFormat('j F Y, H:i').' WIB';

        $isi = $format === 'pdf'
            ? LaporanKegiatan::pdf($laporan, $oleh, $waktu)
            : LaporanKegiatan::xlsx($laporan, $oleh, $waktu);

        $mime = $format === 'pdf'
            ? 'application/pdf'
            : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

        return response($isi, 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'attachment; filename="'.$laporan['nama_file'].'.'.$format.'"',
            'Content-Length' => (string) strlen($isi),
            'Cache-Control' => 'no-store',
        ]);
    }
}
