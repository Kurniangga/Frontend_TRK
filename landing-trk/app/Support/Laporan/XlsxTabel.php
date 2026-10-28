<?php

namespace App\Support\Laporan;

use RuntimeException;
use ZipArchive;

/**
 * Pembuat file Excel (.xlsx) sederhana tanpa paket tambahan (butuh ekstensi PHP "zip",
 * sudah aktif di XAMPP/Laragon dan di CI proyek).
 *
 * Susunan lembar: baris judul/info di atas, satu baris kosong, header tabel (biru), lalu data.
 * Header dibekukan, ada filter otomatis, dan kertas diatur landscape.
 *
 * $kolom = [['label' => 'No', 'lebar' => 6, 'rata' => 'C', 'angka' => true], ...]   lebar = jumlah karakter
 * $data  = [[1, 'Teks', ...], ...]
 */
class XlsxTabel
{
    public function __construct(
        private string $namaLembar,
        private string $judul,
        private array $info,
        private array $kolom,
        private array $data,
        private string $catatan = '',
    ) {}

    public function render(): string
    {
        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('Ekstensi PHP "zip" belum aktif. Aktifkan extension=zip di php.ini lalu restart server.');
        }

        $tmp = tempnam(sys_get_temp_dir(), 'xlsx');
        $zip = new ZipArchive;
        if ($zip->open($tmp, ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Gagal membuat berkas Excel sementara.');
        }

        $zip->addFromString('[Content_Types].xml', $this->contentTypes());
        $zip->addFromString('_rels/.rels', $this->relsRoot());
        $zip->addFromString('xl/workbook.xml', $this->workbook());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->relsWorkbook());
        $zip->addFromString('xl/styles.xml', $this->styles());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->sheet());
        $zip->close();

        $bytes = (string) file_get_contents($tmp);
        @unlink($tmp);

        return $bytes;
    }

    /* ---------- bagian-bagian berkas ---------- */

    private function contentTypes(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            .'</Types>';
    }

    private function relsRoot(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            .'</Relationships>';
    }

    private function workbook(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .'<sheets><sheet name="'.$this->x(mb_substr(preg_replace('/[\\\\\/?*\[\]:]/', ' ', $this->namaLembar), 0, 31)).'" sheetId="1" r:id="rId1"/></sheets>'
            .'</workbook>';
    }

    private function relsWorkbook(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            .'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            .'</Relationships>';
    }

    /** Gaya sel: 0 biasa, 1 judul, 2 header tabel, 3 isi (kiri), 4 isi (tengah), 5 catatan kecil. */
    private function styles(): string
    {
        $border = '<border><left style="thin"><color rgb="FFC7C7CC"/></left><right style="thin"><color rgb="FFC7C7CC"/></right>'
            .'<top style="thin"><color rgb="FFC7C7CC"/></top><bottom style="thin"><color rgb="FFC7C7CC"/></bottom><diagonal/></border>';

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<fonts count="5">'
            .'<font><sz val="11"/><name val="Calibri"/></font>'
            .'<font><b/><sz val="14"/><name val="Calibri"/></font>'
            .'<font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font>'
            .'<font><sz val="11"/><name val="Calibri"/></font>'
            .'<font><i/><sz val="9"/><color rgb="FF6B6B72"/><name val="Calibri"/></font>'
            .'</fonts>'
            .'<fills count="3"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill>'
            .'<fill><patternFill patternType="solid"><fgColor rgb="FF2277FF"/><bgColor indexed="64"/></patternFill></fill></fills>'
            .'<borders count="2"><border><left/><right/><top/><bottom/><diagonal/></border>'.$border.'</borders>'
            .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            .'<cellXfs count="6">'
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            .'<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
            .'<xf numFmtId="0" fontId="2" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>'
            .'<xf numFmtId="0" fontId="3" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment horizontal="left" vertical="top" wrapText="1"/></xf>'
            .'<xf numFmtId="0" fontId="3" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="top" wrapText="1"/></xf>'
            .'<xf numFmtId="0" fontId="4" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
            .'</cellXfs>'
            .'<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            .'</styleSheet>';
    }

    private function sheet(): string
    {
        $jumlahKolom = count($this->kolom);
        $akhir = $this->huruf($jumlahKolom);
        $baris = [];
        $r = 1;

        $baris[] = '<row r="'.$r.'" ht="22" customHeight="1">'.$this->sel('A', $r, $this->judul, 1).'</row>';
        $r++;
        foreach ($this->info as $teks) {
            $baris[] = '<row r="'.$r.'">'.$this->sel('A', $r, $teks, 0).'</row>';
            $r++;
        }
        $r++;   // baris kosong

        $barisHeader = $r;
        $sel = '';
        foreach ($this->kolom as $i => $k) {
            $sel .= $this->sel($this->huruf($i + 1), $r, $k['label'], 2);
        }
        $baris[] = '<row r="'.$r.'" ht="26" customHeight="1">'.$sel.'</row>';
        $r++;

        $awalData = $r;
        foreach ($this->data as $row) {
            $sel = '';
            foreach ($this->kolom as $i => $k) {
                $sel .= $this->sel($this->huruf($i + 1), $r, $row[$i] ?? '', ($k['rata'] ?? 'L') === 'C' ? 4 : 3, ! empty($k['angka']));
            }
            $baris[] = '<row r="'.$r.'">'.$sel.'</row>';
            $r++;
        }
        $barisAkhir = max($r - 1, $barisHeader);

        if ($this->catatan !== '') {
            $r++;
            $baris[] = '<row r="'.$r.'">'.$this->sel('A', $r, $this->catatan, 5).'</row>';
        }

        $cols = '';
        foreach ($this->kolom as $i => $k) {
            $cols .= '<col min="'.($i + 1).'" max="'.($i + 1).'" width="'.$k['lebar'].'" customWidth="1"/>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<sheetPr><pageSetUpPr fitToPage="1"/></sheetPr>'
            .'<dimension ref="A1:'.$akhir.$r.'"/>'
            .'<sheetViews><sheetView workbookViewId="0" showGridLines="0">'
            .'<pane ySplit="'.$barisHeader.'" topLeftCell="A'.$awalData.'" activePane="bottomLeft" state="frozen"/>'
            .'<selection pane="bottomLeft" activeCell="A'.$awalData.'" sqref="A'.$awalData.'"/>'
            .'</sheetView></sheetViews>'
            .'<sheetFormatPr defaultRowHeight="15"/>'
            .'<cols>'.$cols.'</cols>'
            .'<sheetData>'.implode('', $baris).'</sheetData>'
            .'<autoFilter ref="A'.$barisHeader.':'.$akhir.$barisAkhir.'"/>'
            .'<pageMargins left="0.5" right="0.5" top="0.6" bottom="0.6" header="0.3" footer="0.3"/>'
            .'<pageSetup paperSize="9" orientation="landscape" fitToWidth="1" fitToHeight="0"/>'
            .'</worksheet>';
    }

    /** Satu sel: angka (bila $angka dan numerik) atau teks inline. */
    private function sel(string $kolom, int $baris, mixed $nilai, int $gaya, bool $angka = false): string
    {
        $ref = $kolom.$baris;

        if ($angka && is_numeric($nilai)) {
            return '<c r="'.$ref.'" s="'.$gaya.'"><v>'.$nilai.'</v></c>';
        }

        $teks = (string) $nilai;
        if ($teks === '') {
            return '<c r="'.$ref.'" s="'.$gaya.'"/>';
        }

        return '<c r="'.$ref.'" s="'.$gaya.'" t="inlineStr"><is><t xml:space="preserve">'.$this->x($teks).'</t></is></c>';
    }

    private function huruf(int $n): string
    {
        $s = '';
        while ($n > 0) {
            $n--;
            $s = chr(65 + $n % 26).$s;
            $n = intdiv($n, 26);
        }

        return $s;
    }

    /** Escape XML + buang karakter kontrol yang tidak sah di XML 1.0. */
    private function x(string $s): string
    {
        $s = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $s);

        return htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
