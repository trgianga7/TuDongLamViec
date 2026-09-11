<?php

namespace Tests\Browser\Reader\QuyDauTuReader;

use DateTime;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class DuAnDangThucHienReader
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

            $excelRow = $index + 2;

            $item = array_combine($header, $row);

            $files = self::tach($item['files'] ?? '');

            $data[] = [

                'ten_du_an' => trim($item['ten_du_an']),
                'dia_chi' => trim($item['dia_chi']),

                'doanh_nghiep_id' => (string)$item['doanh_nghiep_id'],
                'linh_vuc_cho_vay_id' => (string)$item['linh_vuc_cho_vay_id'],

                'thong_tin_chung' => trim($item['thong_tin_chung']),
                'dai_dien_chu_dau_tu' => trim($item['dai_dien_chu_dau_tu']),

                'loai_du_an' => (string)$item['loai_du_an'],
                'quy_che' => (string)$item['quy_che'],

                'tong_muc_dau_tu' => trim($item['tong_muc_dau_tu']),
                'tong_so_von_ky_hop_dong' => trim($item['tong_so_von_ky_hop_dong']),
                'tong_du_no' => trim($item['tong_du_no']),
                'so_con_giai_ngan' => trim($item['so_con_giai_ngan']),

                'ngay_tinh_tren_he_thong_moi'
                    => self::formatDate($sheet->getCell("M{$excelRow}")),

                'tong_goc_da_thu' => trim($item['tong_goc_da_thu']),
                'tong_lai_da_thu' => trim($item['tong_lai_da_thu']),

                'so_thang_vay' => trim($item['so_thang_vay']),
                'lai_trong_han' => trim($item['lai_trong_han']),
                'lai_qua_han' => trim($item['lai_qua_han']),
                'lai_cham_tra' => trim($item['lai_cham_tra']),

                'chu_ky_tra_goc' => (string)$item['chu_ky_tra_goc'],
                'chu_ky_tra_lai' => (string)$item['chu_ky_tra_lai'],

                'files' => $files,

                'so_tai_khoan_khac' => trim($item['so_tai_khoan_khac']),
                'ghi_chu' => trim($item['ghi_chu']),
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

            $dt = DateTime::createFromFormat($format, $value);

            if ($dt !== false) {
                return $dt->format('d/m/Y');
            }
        }

        return $value;
    }

}