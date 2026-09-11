<?php

namespace Tests\Browser\Reader;

use PhpOffice\PhpSpreadsheet\IOFactory;

class PhongHopReader
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
                'shortName' => $item['shortName'] ?? '',
            ];
        }

        return $data;
    }
}