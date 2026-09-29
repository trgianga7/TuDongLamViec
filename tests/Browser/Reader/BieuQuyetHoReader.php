<?php

namespace Tests\Browser\Reader;

use PhpOffice\PhpSpreadsheet\IOFactory;

class BieuQuyetHoReader
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
                'ten_cuoc_hop' => trim($item['ten_cuoc_hop']),
                'thanh_vien'   => trim($item['thanh_vien']),
                'ket_qua'      => trim($item['ket_qua']),
            ];
        }

        return $data;
    }
}