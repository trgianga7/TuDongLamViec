<?php

namespace Tests\Browser\Reader;

use PhpOffice\PhpSpreadsheet\IOFactory;

class DanhMucKienNghi_LinhVucReader
{
    public static function read(string $path): array
    {
        $sheet = IOFactory::load($path)->getActiveSheet();

        $rows = $sheet->toArray(null, true, true, false);

        $header = array_shift($rows);

        $data = [];

        foreach ($rows as $row) {

            if (trim((string)$row[0]) === '') {
                continue;
            }

            $item = array_combine($header, $row);

            $data[] = [
                'ten'            => trim($item['ten']),
                'nhom_linh_vuc'  => (string)$item['nhom_linh_vuc'],
                'mo_ta'          => trim((string)$item['mo_ta']),
            ];
        }

        return $data;
    }
}