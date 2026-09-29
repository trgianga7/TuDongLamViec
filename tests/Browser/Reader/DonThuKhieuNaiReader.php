<?php

namespace Tests\Browser\Reader;

use DateTime;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class DonThuKhieuNaiReader
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

            // ===== Hồ sơ đính kèm =====
            $files = self::tach($item['file_ho_so'] ?? '');
            $contents = self::tach($item['noi_dung_ho_so'] ?? '');

            $hoso = [];

            foreach ($contents as $i => $text) {
                $hoso[] = [
                    'file' => $files[$i] ?? '',
                    'noi_dung' => $text,
                ];
            }

            $data[] = [

                // Dashboard
                'cap_don' => trim($item['cap_don']),

                // Tiếp nhận
                'so_thu_tu' => trim($item['so_thu_tu']),
                'ngay_nhap_don' => self::formatDate(
                    $sheet->getCell("C{$excelRow}")
                ),
                'tinh_trang_xu_ly' => trim($item['tinh_trang_xu_ly']),
                'don_doc' => trim($item['don_doc']),
                'don_luu' => trim($item['don_luu']),

                'don_trung' => self::bool($item['don_trung']),
                'don_khong_du_dieu_kien'
                    => self::bool($item['don_khong_du_dieu_kien']),
                'don_du_dieu_kien'
                    => self::bool($item['don_du_dieu_kien']),

                // Người gửi
                'doi_tuong_gui_don' => trim($item['doi_tuong_gui_don']),
                'thuoc_to_chuc' => trim($item['thuoc_to_chuc']),
                'ho_ten_chu_don' => trim($item['ho_ten_chu_don']),
                'cmt' => (string)$item['cmt'],
                'sdt_email' => trim($item['sdt_email']),
                'phuong_xa' => trim($item['phuong_xa']),
                'dia_chi' => trim($item['dia_chi']),
                'nghe_nghiep' => trim($item['nghe_nghiep']),
                'nguon_don' => trim($item['nguon_don']),
                'loai_don' => trim($item['loai_don']),
                'linh_vuc_don' => trim($item['linh_vuc_don']),

                // Đối tượng bị KN/TC
                'doi_tuong_bi_khieu_nai'
                    => trim($item['doi_tuong_bi_khieu_nai']),
                'ho_ten_bi_khieu_nai'
                    => trim($item['ho_ten_bi_khieu_nai']),
                'ten_co_quan' => trim($item['ten_co_quan']),
                'dia_chi_bi_khieu_nai'
                    => trim($item['dia_chi_bi_khieu_nai']),
                'nghe_nghiep_bi_khieu_nai'
                    => trim($item['nghe_nghiep_bi_khieu_nai']),

                // Lãnh đạo
                'noi_nhan_don' => trim($item['noi_nhan_don']),
                'lanh_dao_chi_dao'
                    => trim($item['lanh_dao_chi_dao']),
                'chuyen_vien_xu_ly'
                    => trim($item['chuyen_vien_xu_ly']),

                'coquanphoihopxuly'
                    => trim($item['coquanphoihopxuly']),
                'coquanphoihopxuly1'
                    => trim($item['coquanphoihopxuly1']),
                'coquanphoihopxuly2'
                    => trim($item['coquanphoihopxuly2']),
                'coquanphoihopxuly3'
                    => trim($item['coquanphoihopxuly3']),
                'coquanphoihopxuly4'
                    => trim($item['coquanphoihopxuly4']),

                // Hồ sơ
                'ho_so' => $hoso,
            ];
        }

        return $data;
    }

    private static function bool($v): bool
    {
        return in_array(strtolower(trim((string)$v)), [
            '1',
            'true',
            'yes'
        ]);
    }

    private static function tach(string $text): array
    {
        if (trim($text) === '') {
            return [];
        }

        return array_values(array_filter(
            array_map('trim', explode('|', $text))
        ));
    }

    private static function formatDate($cell): string
    {
        $value = $cell->getValue();

        if (is_numeric($value) && Date::isDateTime($cell)) {
            return Date::excelToDateTimeObject($value)
                ->format('d/m/Y');
        }

        $value = trim((string)$value);

        foreach ([
            'd/m/Y',
            'n/j/Y',
            'Y-m-d',
            'm/d/Y'
        ] as $f) {

            $dt = DateTime::createFromFormat($f, $value);

            if ($dt) {
                return $dt->format('d/m/Y');
            }
        }

        return $value;
    }
}