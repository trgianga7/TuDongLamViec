<?php

namespace Tests\Browser\Reader;

use PhpOffice\PhpSpreadsheet\IOFactory;

class KienNghiCuTriReader
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
                'loai_kien_nghi'      => trim(strtolower($item['loai_kien_nghi'])),

                'khoa_hop'            => trim($item['khoa_hop']),
                'ky_hop'              => trim($item['ky_hop']),
                'linh_vuc'            => trim($item['linh_vuc']),
                'phuong_xa'           => trim($item['phuong_xa']),

                'xom'                 => trim($item['xom']),
                'cu_tri'              => trim($item['cu_tri']),

                'don_vi_giai_quyet'   => trim($item['don_vi_giai_quyet']),
                'trang_thai'          => trim($item['trang_thai']),

                'noi_dung'            => trim($item['noi_dung']),

                'ten_file'            => trim($item['ten_file']),
                'file_dinh_kem'       => trim($item['file_dinh_kem']),
            ];
        }

        return $data;
    }
}