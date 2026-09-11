<?php

namespace Tests\Browser\Reader;

use PhpOffice\PhpSpreadsheet\IOFactory;
use DateTime;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PhieuLayYKienReader
{
    public static function read(string $path): array
    {
        $sheet = IOFactory::load($path)->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, false);

        $header = array_shift($rows);
        $data = [];

        foreach ($rows as $index => $row) {

            if (trim((string)$row[0]) === '') continue;

            $excelRow = $index + 2;

            $item = array_combine($header, $row);

            $noiDung = self::tach($item['bieu_quyet_noi_dung'] ?? '');
            $files    = self::tach($item['bieu_quyet_files'] ?? '');

            $bieuQuyet = [];

            foreach ($noiDung as $i => $text) {

                $bieuQuyet[] = [
                    'noi_dung' => $text,
                    'files' => self::tachPhay($files[$i] ?? ''),
                ];
            }

            $tenTL  = self::tach($item['tai_lieu_chung_ten'] ?? '');
            $fileTL = self::tach($item['tai_lieu_chung_file'] ?? '');

            $taiLieu = [];

            foreach ($tenTL as $i => $ten) {

                $taiLieu[] = [
                    'ten' => $ten,
                    'file' => $fileTL[$i] ?? '',
                ];
            }

            $data[] = [
                'the_thuc1' => $item['the_thuc1'],
                'the_thuc2' => $item['the_thuc2'],
                'the_thuc3' => $item['the_thuc3'],
                'so_van_ban' => $item['so_van_ban'],
                'trich_yeu_ngan' => $item['trich_yeu_ngan'],
                'kinh_gui' => $item['kinh_gui'],
                'noi_dung_nghien_cuu' => $item['noi_dung_nghien_cuu'],
                'can_cu' => $item['can_cu'],
                'ngay' => self::formatDate($sheet->getCell("I{$excelRow}")),
                'thang' => $item['thang'],
                'nam' => $item['nam'],
                'han_tra_loi' => self::formatDate($sheet->getCell("L{$excelRow}")),
                'gio_tra_loi' => $item['gio_tra_loi'],
                'loai_phieu' => $item['loai_phieu'],
                'bieu_quyet' => $bieuQuyet,
                'tai_lieu_chung' => $taiLieu,
            ];
        }

        return $data;
    }

    private static function tach(string $text): array
    {
        if ($text === '') return [];

        return array_map('trim', explode('|', $text));
    }

    private static function tachPhay(string $text): array
    {
        if ($text === '') return [];

        return array_map('trim', explode(',', $text));
    }

    private static function formatDate($cell): string
    {
        $value = $cell->getValue();

        if (is_numeric($value) && Date::isDateTime($cell)) {
            return Date::excelToDateTimeObject($value)
                ->format('d/m/Y');
        }

        $value = trim((string)$value);

        $formats = [
            'd/m/Y',
            'd/m/Y H:i',
            'n/j/Y',
            'n/j/Y G:i',
            'm/d/Y',
            'Y-m-d',
        ];

        foreach ($formats as $format) {
            $dt = DateTime::createFromFormat($format, $value);

            if ($dt !== false) {
                return $dt->format('d/m/Y');
            }
        }

        return $value;
    }
}