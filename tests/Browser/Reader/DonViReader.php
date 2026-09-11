<?php

namespace Tests\Browser\Reader;

use PhpOffice\PhpSpreadsheet\IOFactory;

class DonViReader
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
                'ten_don_vi'     => $item['ten_don_vi'],
                'parent_name'    => $item['parent_name'] ?? '',
                'ten_viet_tat'   => $item['ten_viet_tat'] ?? '',
                'ma_hanh_chinh'  => $item['ma_hanh_chinh'] ?? '',
                'dia_chi'        => $item['dia_chi'] ?? '',
                'dien_thoai'     => $item['dien_thoai'] ?? '',
                'email'          => $item['email'] ?? '',
                'cap_to_chuc'    => (string)$item['cap_to_chuc'],
                'dieu_hanh'      => (string)$item['dieu_hanh'],
            ];
        }

        return $data;
    }
}