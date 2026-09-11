<?php

namespace Tests\Browser\Reader;

use PhpOffice\PhpSpreadsheet\IOFactory;

class KyHopReader
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
                'name' => $item['name'],
                'khoa_hop_name' => $item['khoa_hop_name'],
                'bat_dau' => $item['bat_dau'],
                'ket_thuc' => $item['ket_thuc'],
                'dia_diem_nhap' => $item['dia_diem_nhap'] ?? '',
                'mo_ta' => $item['mo_ta'] ?? '',
                'type' => (string)$item['type'],
            ];
        }

        return $data;
    }
}