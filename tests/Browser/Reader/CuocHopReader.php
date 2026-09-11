<?php

namespace Tests\Browser\Reader;

use DateTime;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class CuocHopReader
{
    public static function read(string $path): array
    {
        $sheet = IOFactory::load($path)->getActiveSheet();

        $rows = $sheet->toArray(null, true, true, false);
        $header = array_shift($rows);

        $data = [];

        foreach ($rows as $index => $row) {

            if (trim((string)$row[0]) === '') {
                continue;
            }

            $excelRow = $index + 2;
            $item = array_combine($header, $row);

            $data[] = [
                'tieu_de' => trim($item['tieu_de']),
                'noi_dung' => trim($item['noi_dung']),

                'khoa_hop_id' => (string)$item['khoa_hop_id'],
                'ky_hop_id' => (string)$item['ky_hop_id'],

                'ngay' => self::formatDate($sheet->getCell("E{$excelRow}")),
                'gio' => trim($item['gio']),

                'ngay_ket_thuc' => self::formatDate($sheet->getCell("G{$excelRow}")),
                'gio_ket_thuc' => trim($item['gio_ket_thuc']),

                'phong_hop' => (string)$item['phong_hop'],
                'dia_diem' => trim($item['dia_diem']),

                'ngay_ket_thuc_tai_tai_lieu'
                    => self::formatDate($sheet->getCell("K{$excelRow}")),

                'gio_ket_thuc_tai_tai_lieu'
                    => trim($item['gio_ket_thuc_tai_tai_lieu']),

                'thong_bao_ket_luan'
                    => (string)$item['thong_bao_ket_luan'],

                'phien_hop' => (string)$item['phien_hop'],
            ];
        }

        return $data;
    }

    private static function formatDate($cell): string
    {
        $value = $cell->getValue();

        if (is_numeric($value) && Date::isDateTime($cell)) {
            return Date::excelToDateTimeObject($value)->format('d/m/Y');
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