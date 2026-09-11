<?php

namespace Tests\Browser\Reader;

use PhpOffice\PhpSpreadsheet\IOFactory;

class NoiDungGiamSatReader
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

            $tenTaiLieu = self::tach($item['ten_tai_lieu'] ?? '');
            $files = self::tach($item['files'] ?? '');

            $data[] = [
                'noi_dung_chuong_trinh' => $item['noi_dung_chuong_trinh'],
                'noi_dung'              => $item['noi_dung'],

                'doi_tuong_ids' => array_map(
                    'trim',
                    explode(',', (string)($item['doi_tuong'] ?? ''))
                ),

                
                'ten_tai_lieu' => $tenTaiLieu,
                'files' => $files,
            ];
        }

        return $data;
    }

    private static function tach(string $text): array
    {
        if (trim($text) === '') {
            return [];
        }

        return array_map('trim', explode('|', $text));
    }
}