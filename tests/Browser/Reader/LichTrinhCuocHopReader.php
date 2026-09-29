<?php

namespace Tests\Browser\Reader;

use PhpOffice\PhpSpreadsheet\IOFactory;

class LichTrinhCuocHopReader
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
                'thoi_gian'    => trim($item['thoi_gian']),
                'tieu_de'      => trim($item['tieu_de']),
                'noi_dung'     => trim($item['noi_dung']),
            ];
        }

        return $data;
    }
}