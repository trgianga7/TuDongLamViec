<?php

namespace Tests\Browser\Reader;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class UserReader
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

            $item = array_combine($header, $row);

            $excelNgaySinh = $index + 2;

            $data[] = [
                'name' => trim($item['name']),
                'ngay_sinh' => self::formatDate($sheet->getCell("B{$excelNgaySinh}")),
                'username' => trim($item['username']),
                'password' => trim($item['password']),
                'role_id' => (string)$item['role_id'],
                'phone' => (string)$item['phone'],
                'email' => trim($item['email']),
                'chuc_vu_id' => trim($item['chuc_vu_id']),
                'don_vi_id' => (string)$item['don_vi_id'],
                'vai_tro_hdnd' => (string) $item['vai_tro_hdnd'],
                'la_dai_bieu' => $item['la_dai_bieu'] == 1,
                'tra_loi_chat_van' => $item['tra_loi_chat_van'] == 1,
                'status' => $item['status'] == 1,
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
            $dt = \DateTime::createFromFormat($format, $value);
            if ($dt !== false) {
                return $dt->format('d/m/Y');
            }
        }

        return $value;
    }
}