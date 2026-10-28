<?php

namespace App\Support\Laporan;

/**
 * Pembuat PDF tabel sederhana (tanpa paket tambahan): A4 landscape, judul, tabel dengan
 * header berulang tiap halaman, teks otomatis turun baris, dan nomor halaman.
 * Font Helvetica bawaan PDF (tidak perlu menyematkan font).
 *
 * Pemakaian:
 *   $pdf = new PdfTabel('LAPORAN ...', ['baris info 1', 'baris info 2'], $kolom, $data, 'Dicetak oleh ...');
 *   $bytes = $pdf->render();
 *
 * $kolom = [['label' => 'No', 'lebar' => 30, 'rata' => 'C'], ...]   rata: L | C | R, lebar relatif
 * $data  = [['1', 'Teks', ...], ...]   (semua sel string)
 */
class PdfTabel
{
    private const LEBAR = 841.89;   // A4 landscape (pt)

    private const TINGGI = 595.28;

    private const MARGIN = 36.0;

    private const UKURAN = 8.5;     // ukuran font isi tabel

    private const PAD = 4.0;

    /** Lebar karakter Helvetica (ASCII 32-126) per 1000 em. */
    private const REG = [
        278, 278, 355, 556, 556, 889, 667, 191, 333, 333, 389, 584, 278, 333, 278, 278,
        556, 556, 556, 556, 556, 556, 556, 556, 556, 556, 278, 278, 584, 584, 584, 556,
        1015, 667, 667, 722, 722, 667, 611, 778, 722, 278, 500, 667, 556, 833, 722, 778,
        667, 778, 722, 667, 611, 722, 667, 944, 667, 667, 611, 278, 278, 278, 469, 556,
        333, 556, 556, 500, 556, 556, 278, 556, 556, 222, 222, 500, 222, 833, 556, 556,
        556, 556, 333, 500, 278, 556, 500, 722, 500, 500, 500, 334, 260, 334, 584,
    ];

    private const BOLD = [
        278, 333, 474, 556, 556, 889, 722, 238, 333, 333, 389, 584, 278, 333, 278, 278,
        556, 556, 556, 556, 556, 556, 556, 556, 556, 556, 333, 333, 584, 584, 584, 611,
        975, 722, 722, 722, 722, 667, 611, 778, 722, 278, 556, 722, 611, 833, 722, 778,
        667, 778, 722, 667, 611, 722, 667, 944, 667, 667, 611, 333, 278, 333, 584, 556,
        333, 556, 611, 556, 611, 556, 333, 611, 611, 278, 278, 556, 278, 889, 611, 611,
        611, 611, 389, 556, 333, 611, 556, 778, 556, 556, 500, 389, 280, 389, 584,
    ];

    /** @var string[] */
    private array $halaman = [];

    private string $isi = '';

    private float $y = 0;

    public function __construct(
        private string $judul,
        private array $info,
        private array $kolom,
        private array $data,
        private string $footer = '',
    ) {}

    public function render(): string
    {
        $this->hitungLebarKolom();
        $this->halamanBaru(true);

        $this->barisHeader();
        $zebra = false;
        foreach ($this->data as $baris) {
            $sel = [];
            $maks = 1;
            foreach ($this->kolom as $i => $k) {
                $teks = $this->wrap((string) ($baris[$i] ?? ''), $k['lebar'] - 2 * self::PAD, self::UKURAN, false);
                $sel[$i] = $teks;
                $maks = max($maks, count($teks));
            }
            $tinggi = $maks * self::UKURAN * 1.25 + 2 * self::PAD;

            if ($this->y + $tinggi > self::TINGGI - self::MARGIN - 18) {
                $this->halamanBaru(false);
                $this->barisHeader();
            }
            $this->gambarBaris($sel, $tinggi, $zebra ? [0.965, 0.969, 0.98] : [1, 1, 1], false);
            $zebra = ! $zebra;
        }

        $this->tutupHalaman();

        return $this->susunBerkas();
    }

    /* ---------- tata letak ---------- */

    private function hitungLebarKolom(): void
    {
        $total = array_sum(array_column($this->kolom, 'lebar'));
        $tersedia = self::LEBAR - 2 * self::MARGIN;
        foreach ($this->kolom as &$k) {
            $k['lebar'] = $k['lebar'] / $total * $tersedia;
        }
        unset($k);
    }

    private function halamanBaru(bool $pertama): void
    {
        if ($this->isi !== '') {
            $this->tutupHalaman();
        }
        $this->isi = '';
        $this->y = self::MARGIN;   // jarak dari atas halaman

        if ($pertama) {
            $this->teks(self::MARGIN, $this->y + 14, $this->judul, 14, true);
            $this->y += 20;
            foreach ($this->info as $baris) {
                $this->teks(self::MARGIN, $this->y + 10, $baris, 9.5, false);
                $this->y += 13;
            }
            $this->y += 8;
        }
    }

    private function tutupHalaman(): void
    {
        $this->halaman[] = $this->isi;
        $this->isi = '';
    }

    private function barisHeader(): void
    {
        $sel = [];
        $maks = 1;
        foreach ($this->kolom as $i => $k) {
            $sel[$i] = $this->wrap($k['label'], $k['lebar'] - 2 * self::PAD, self::UKURAN, true);
            $maks = max($maks, count($sel[$i]));
        }
        $this->gambarBaris($sel, $maks * self::UKURAN * 1.25 + 2 * self::PAD + 2, [0.133, 0.467, 1.0], true);
    }

    /** Gambar satu baris (sel sudah dipecah per baris teks). */
    private function gambarBaris(array $sel, float $tinggi, array $warna, bool $header): void
    {
        $x = self::MARGIN;
        $yAtas = self::TINGGI - $this->y;   // koordinat PDF (asal kiri-bawah)

        foreach ($this->kolom as $i => $k) {
            $this->isi .= sprintf(
                "q %.3F %.3F %.3F rg 0.78 0.78 0.80 RG 0.6 w %.2F %.2F %.2F %.2F re B Q\n",
                $warna[0], $warna[1], $warna[2], $x, $yAtas - $tinggi, $k['lebar'], $tinggi
            );

            $yTeks = $this->y + self::PAD + self::UKURAN;
            foreach ($sel[$i] as $baris) {
                $lebarTeks = $this->lebar($baris, self::UKURAN, $header);
                $rata = $header ? 'C' : ($k['rata'] ?? 'L');
                $tx = match ($rata) {
                    'C' => $x + ($k['lebar'] - $lebarTeks) / 2,
                    'R' => $x + $k['lebar'] - self::PAD - $lebarTeks,
                    default => $x + self::PAD,
                };
                $this->teks($tx, $yTeks, $baris, self::UKURAN, $header, $header ? [1, 1, 1] : [0, 0, 0]);
                $yTeks += self::UKURAN * 1.25;
            }
            $x += $k['lebar'];
        }

        $this->y += $tinggi;
    }

    /** Tulis teks; $yDariAtas = jarak baseline dari atas halaman. */
    private function teks(float $x, float $yDariAtas, string $teks, float $ukuran, bool $tebal, array $warna = [0, 0, 0]): void
    {
        $this->isi .= sprintf(
            "BT %.3F %.3F %.3F rg /%s %.2F Tf %.2F %.2F Td (%s) Tj ET\n",
            $warna[0], $warna[1], $warna[2], $tebal ? 'F2' : 'F1', $ukuran, $x, self::TINGGI - $yDariAtas, $this->escape($teks)
        );
    }

    /* ---------- teks ---------- */

    private function win1252(string $s): string
    {
        $r = @iconv('UTF-8', 'CP1252//TRANSLIT//IGNORE', $s);

        return $r === false ? preg_replace('/[^\x20-\x7E]/', '?', $s) : $r;
    }

    private function escape(string $s): string
    {
        return strtr($this->win1252($s), ['\\' => '\\\\', '(' => '\\(', ')' => '\\)', "\r" => '', "\n" => ' ']);
    }

    private function lebar(string $s, float $ukuran, bool $tebal): float
    {
        $tabel = $tebal ? self::BOLD : self::REG;
        $total = 0;
        $s = $this->win1252($s);
        foreach ($s === '' ? [] : str_split($s) as $c) {
            $o = ord($c);
            $total += ($o >= 32 && $o <= 126) ? $tabel[$o - 32] : 556;
        }

        return $total * $ukuran / 1000;
    }

    /** Pecah teks menjadi baris-baris yang muat dalam $maks (pt). */
    private function wrap(string $teks, float $maks, float $ukuran, bool $tebal): array
    {
        $teks = trim(preg_replace('/\s+/', ' ', $teks));
        if ($teks === '') {
            return [''];
        }

        $baris = [];
        $sekarang = '';
        foreach (explode(' ', $teks) as $kata) {
            // kata yang lebih panjang dari kolom dipotong per karakter
            while ($this->lebar($kata, $ukuran, $tebal) > $maks && mb_strlen($kata) > 1) {
                $potong = '';
                foreach (preg_split('//u', $kata, -1, PREG_SPLIT_NO_EMPTY) as $ch) {
                    if ($this->lebar($potong.$ch, $ukuran, $tebal) > $maks) {
                        break;
                    }
                    $potong .= $ch;
                }
                if ($potong === '') {
                    break;
                }
                if ($sekarang !== '') {
                    $baris[] = $sekarang;
                    $sekarang = '';
                }
                $baris[] = $potong;
                $kata = mb_substr($kata, mb_strlen($potong));
            }

            $coba = $sekarang === '' ? $kata : $sekarang.' '.$kata;
            if ($sekarang !== '' && $this->lebar($coba, $ukuran, $tebal) > $maks) {
                $baris[] = $sekarang;
                $sekarang = $kata;
            } else {
                $sekarang = $coba;
            }
        }
        if ($sekarang !== '') {
            $baris[] = $sekarang;
        }

        return $baris;
    }

    /* ---------- berkas PDF ---------- */

    private function susunBerkas(): string
    {
        $jumlah = count($this->halaman);
        $objek = [];

        // 1 katalog, 2 daftar halaman, 3-4 font, lalu pasangan (halaman, isi) tiap halaman, terakhir info
        $kids = [];
        for ($i = 0; $i < $jumlah; $i++) {
            $kids[] = (5 + $i * 2).' 0 R';
        }

        $objek[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objek[2] = '<< /Type /Pages /Kids ['.implode(' ', $kids).'] /Count '.$jumlah.' >>';
        $objek[3] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>';
        $objek[4] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>';

        foreach ($this->halaman as $i => $isi) {
            // footer + nomor halaman
            $kiri = $this->escape($this->footer);
            $kanan = $this->escape('Halaman '.($i + 1).' dari '.$jumlah);
            $lebarKanan = $this->lebar('Halaman '.($i + 1).' dari '.$jumlah, 8, false);
            $isi .= sprintf("BT 0.4 0.4 0.4 rg /F1 8 Tf %.2F %.2F Td (%s) Tj ET\n", self::MARGIN, 22, $kiri);
            $isi .= sprintf("BT 0.4 0.4 0.4 rg /F1 8 Tf %.2F %.2F Td (%s) Tj ET\n", self::LEBAR - self::MARGIN - $lebarKanan, 22, $kanan);

            $idHal = 5 + $i * 2;
            $idIsi = $idHal + 1;
            $objek[$idHal] = sprintf(
                '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 %.2F %.2F] /Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /Contents %d 0 R >>',
                self::LEBAR, self::TINGGI, $idIsi
            );
            $objek[$idIsi] = '<< /Length '.strlen($isi)." >>\nstream\n".$isi.'endstream';
        }

        $idInfo = 5 + $jumlah * 2;
        $objek[$idInfo] = '<< /Title ('.$this->escape($this->judul).') /Producer (SIKAP TRK) >>';

        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offset = [];
        for ($n = 1; $n <= $idInfo; $n++) {
            $offset[$n] = strlen($pdf);
            $pdf .= $n." 0 obj\n".$objek[$n]."\nendobj\n";
        }

        $xref = strlen($pdf);
        $pdf .= "xref\n0 ".($idInfo + 1)."\n0000000000 65535 f \n";
        for ($n = 1; $n <= $idInfo; $n++) {
            $pdf .= sprintf("%010d 00000 n \n", $offset[$n]);
        }
        $pdf .= 'trailer << /Size '.($idInfo + 1).' /Root 1 0 R /Info '.$idInfo." 0 R >>\nstartxref\n".$xref."\n%%EOF";

        return $pdf;
    }
}
