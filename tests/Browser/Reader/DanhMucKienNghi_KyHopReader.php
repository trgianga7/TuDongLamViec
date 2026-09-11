<?php

namespace Tests\Browser\Reader;

use DateTime;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class DanhMucKienNghi_KyHopReader
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
                'name' => trim($item['name']),
                'bat_dau' => self::formatDate($sheet->getCell("B{$excelRow}")),
                'ket_thuc' => self::formatDate($sheet->getCell("C{$excelRow}")),
                'so_diem_tiep_xuc' => trim((string)$item['so_diem_tiep_xuc']),
                'so_luong_cu_chi' => trim((string)$item['so_luong_cu_chi']),
            ];
        }

        return $data;
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