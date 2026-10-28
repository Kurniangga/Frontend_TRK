<?php

namespace App\Support\Laporan;

/**
 * Menyusun laporan data kegiatan (dosen / mahasiswa) per kurun waktu tahun,
 * lalu mengubahnya menjadi PDF atau Excel.
 */
class LaporanKegiatan
{
    /**
     * @param  string  $jenis  'dosen' | 'mahasiswa'
     * @param  array  $rows  baris dari KegiatanData
     * @param  string  $status  'Disetujui' | 'Diajukan' | ... | 'Semua'
     * @return array{judul: string, info: string[], kolom: array, data: array, nama_file: string, jumlah: int}
     */
    public static function susun(string $jenis, array $rows, int $dari, int $sampai, string $status): array
    {
        $rows = array_values(array_filter($rows, fn ($r) => $r['tahun'] >= $dari && $r['tahun'] <= $sampai
            && ($status === 'Semua' || $r['status'] === $status)));

        usort($rows, fn ($a, $b) => [$a['tanggal_iso'], $a['nama']] <=> [$b['tanggal_iso'], $b['nama']]);

        $mhs = $jenis === 'mahasiswa';
        $periode = $dari === $sampai ? "Tahun $dari" : "Tahun $dari – $sampai";

        $kolom = $mhs
            ? [
                ['label' => 'No', 'lebar' => 30, 'rata' => 'C', 'angka' => true, 'lebar_xls' => 6],
                ['label' => 'Nama Kegiatan', 'lebar' => 210, 'lebar_xls' => 38],
                ['label' => 'Kategori', 'lebar' => 95, 'lebar_xls' => 20],
                ['label' => 'Mahasiswa', 'lebar' => 110, 'lebar_xls' => 22],
                ['label' => 'Tingkat', 'lebar' => 45, 'rata' => 'C', 'lebar_xls' => 9],
                ['label' => 'Kelas', 'lebar' => 40, 'rata' => 'C', 'lebar_xls' => 8],
                ['label' => 'Tanggal', 'lebar' => 80, 'lebar_xls' => 16],
                ['label' => 'Semester', 'lebar' => 55, 'rata' => 'C', 'lebar_xls' => 11],
                ['label' => 'Status', 'lebar' => 65, 'rata' => 'C', 'lebar_xls' => 12],
            ]
            : [
                ['label' => 'No', 'lebar' => 30, 'rata' => 'C', 'angka' => true, 'lebar_xls' => 6],
                ['label' => 'Nama Kegiatan', 'lebar' => 240, 'lebar_xls' => 40],
                ['label' => 'Kategori', 'lebar' => 95, 'lebar_xls' => 18],
                ['label' => 'Dosen', 'lebar' => 130, 'lebar_xls' => 24],
                ['label' => 'Tanggal', 'lebar' => 85, 'lebar_xls' => 16],
                ['label' => 'Semester', 'lebar' => 60, 'rata' => 'C', 'lebar_xls' => 11],
                ['label' => 'Status', 'lebar' => 70, 'rata' => 'C', 'lebar_xls' => 12],
            ];

        $data = [];
        foreach ($rows as $i => $r) {
            $data[] = $mhs
                ? [$i + 1, $r['nama'], $r['kategori'], $r['pengaju'], $r['tingkat'], $r['kelas'], $r['tanggal'], $r['semester'], $r['status']]
                : [$i + 1, $r['nama'], $r['kategori'], $r['dosen'], $r['tanggal'], $r['semester'], $r['status']];
        }

        // Ringkasan per kategori
        $perKategori = array_count_values(array_column($rows, 'kategori'));
        ksort($perKategori);
        $ringkas = [];
        foreach ($perKategori as $k => $n) {
            $ringkas[] = "$k: $n";
        }

        $info = [
            'Program Studi Teknologi Rekayasa Komputer – Politeknik Negeri Semarang',
            'Periode: '.$periode.'   |   Status: '.($status === 'Semua' ? 'Semua status' : $status),
            'Total kegiatan: '.count($rows).($ringkas ? '   ('.implode(', ', $ringkas).')' : ''),
        ];

        return [
            'judul' => 'LAPORAN DATA KEGIATAN '.($mhs ? 'MAHASISWA' : 'DOSEN'),
            'info' => $info,
            'kolom' => $kolom,
            'data' => $data ?: [self::kosong(count($kolom))],
            'nama_file' => 'laporan-kegiatan-'.$jenis.'-'.($dari === $sampai ? $dari : "$dari-$sampai"),
            'jumlah' => count($rows),
        ];
    }

    public static function pdf(array $lap, string $oleh, string $waktuCetak): string
    {
        return (new PdfTabel($lap['judul'], $lap['info'], $lap['kolom'], $lap['data'], 'Dicetak oleh '.$oleh.' pada '.$waktuCetak))->render();
    }

    public static function xlsx(array $lap, string $oleh, string $waktuCetak): string
    {
        // Excel memakai lebar dalam jumlah karakter ('lebar_xls'), PDF memakai lebar relatif ('lebar').
        $kolom = [];
        foreach ($lap['kolom'] as $k) {
            $k['lebar'] = $k['lebar_xls'];
            $kolom[] = $k;
        }

        return (new XlsxTabel('Laporan Kegiatan', $lap['judul'], $lap['info'], $kolom, $lap['data'], 'Dicetak oleh '.$oleh.' pada '.$waktuCetak))->render();
    }

    private static function kosong(int $kolom): array
    {
        $baris = array_fill(0, $kolom, '');
        $baris[1] = 'Tidak ada kegiatan pada periode ini.';

        return $baris;
    }
}
