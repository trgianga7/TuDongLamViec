<?php

namespace Tests\Browser\Reader;

use PhpOffice\PhpSpreadsheet\IOFactory;

class DoiTuongGiamSatReader
{
    public static function read(): array
    {
        $file = base_path('tests/Browser/Excel/DoiTuongGiamSat.xlsx');

        $sheet = IOFactory::load($file)->getActiveSheet();

        $rows = [];

        foreach ($sheet->toArray() as $index => $row) {

            if ($index == 0) continue;

            if (trim($row[0] ?? '') == '') continue;

            $rows[] = [
                'name'      => trim($row[0] ?? ''),
                'shortName' => trim($row[1] ?? ''),
            ];
        }

        return $rows;
    }
}