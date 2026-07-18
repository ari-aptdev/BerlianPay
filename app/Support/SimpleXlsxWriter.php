<?php

namespace App\Support;

use ZipArchive;

/**
 * Generator file .xlsx MINIMAL tapi VALID, tanpa dependency eksternal
 * (maatwebsite/excel & phpoffice/phpspreadsheet sengaja dihindari karena
 * butuh ext-gd yang bikin build Railway gagal — lihat riwayat project).
 *
 * Cuma butuh extension `zip` bawaan PHP yang hampir selalu aktif di semua hosting.
 *
 * Keterbatasan: tidak bisa menyisipkan gambar/logo (butuh struktur OOXML
 * drawing yang jauh lebih kompleks). Logo perumahan tetap muncul di export PDF.
 */
class SimpleXlsxWriter
{
    /**
     * @param  array<int, array<int, string>>  $rows  Baris pertama dianggap header.
     */
    public static function generate(array $rows): string
    {
        $tmpPath = tempnam(sys_get_temp_dir(), 'xlsx');

        $zip = new ZipArchive();
        $zip->open($tmpPath, ZipArchive::OVERWRITE);

        $zip->addFromString('[Content_Types].xml', self::contentTypes());
        $zip->addFromString('_rels/.rels', self::rootRels());
        $zip->addFromString('xl/workbook.xml', self::workbook());
        $zip->addFromString('xl/_rels/workbook.xml.rels', self::workbookRels());
        $zip->addFromString('xl/worksheets/sheet1.xml', self::sheet($rows));

        $zip->close();

        $content = file_get_contents($tmpPath);
        unlink($tmpPath);

        return $content;
    }

    protected static function sheet(array $rows): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $xml .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>';

        foreach ($rows as $rowIndex => $row) {
            $r = $rowIndex + 1;
            $xml .= '<row r="'.$r.'">';
            foreach (array_values($row) as $colIndex => $value) {
                $col = self::columnLetter($colIndex);
                $escaped = htmlspecialchars((string) $value, ENT_QUOTES | ENT_XML1, 'UTF-8');
                $xml .= '<c r="'.$col.$r.'" t="inlineStr"><is><t xml:space="preserve">'.$escaped.'</t></is></c>';
            }
            $xml .= '</row>';
        }

        $xml .= '</sheetData></worksheet>';

        return $xml;
    }

    protected static function columnLetter(int $index): string
    {
        $letter = '';
        $index++;
        while ($index > 0) {
            $mod = ($index - 1) % 26;
            $letter = chr(65 + $mod).$letter;
            $index = intdiv($index - $mod, 26);
        }

        return $letter;
    }

    protected static function contentTypes(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            .'</Types>';
    }

    protected static function rootRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            .'</Relationships>';
    }

    protected static function workbook(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .'<sheets><sheet name="Laporan" sheetId="1" r:id="rId1"/></sheets>'
            .'</workbook>';
    }

    protected static function workbookRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            .'</Relationships>';
    }
}
