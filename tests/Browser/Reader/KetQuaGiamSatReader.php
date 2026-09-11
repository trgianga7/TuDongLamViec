<?php

namespace Tests\Browser\Reader;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class KetQuaGiamSatReader
{
    public static function read(string $path): array
    {
        $sheet = IOFactory::load($path)->getActiveSheet();

        $rows = $sheet->toArray(null, true, true, false);
        $header = array_shift($rows);

        $data = [];

        foreach ($rows as $rowIndex => $row) {

            if (trim((string) ($row[0] ?? '')) === '') {
                continue;
            }

            $item = array_combine($header, $row);

            /*
             * Ngày trong Excel đang được PhpSpreadsheet
             * đọc thành dạng:
             *
             * 8/27/2026
             *
             * Đây là M/D/YYYY.
             *
             * Chuyển thành:
             *
             * 2026-08-27
             *
             * để dùng cho input type="date".
             */

            $ngayNhapGoc = $item['ngay_nhap'] ?? null;

            dump([
                'excel_row' => $rowIndex + 2,
                'ngay_nhap_goc' => $ngayNhapGoc,
                'type' => gettype($ngayNhapGoc),
            ]);

            $ngayNhap = self::formatNgay($ngayNhapGoc);

            dump([
                'ngay_nhap_sau_xu_ly' => $ngayNhap,
            ]);

            $data[] = [
                'noi_dung_chuong_trinh' =>
                    trim((string) $item['noi_dung_chuong_trinh']),

                'kien_nghi' =>
                    trim((string) $item['kien_nghi']),

                'ngay_nhap' =>
                    $ngayNhap,

                'trang_thai' =>
                    (string) $item['trang_thai'],

                'noi_dung' =>
                    $item['noi_dung'],
            ];
        }

        return $data;
    }

    private static function formatNgay($value): string
    {
        if ($value === null || trim((string) $value) === '') {
            throw new \Exception(
                'Ngày nhập đang bị trống'
            );
        }

        /*
         * Excel serial date
         *
         * Ví dụ:
         * 46261
         */
        if (is_numeric($value)) {

            return Date::excelToDateTimeObject(
                (float) $value
            )->format('Y-m-d');
        }

        $value = trim((string) $value);

        /*
         * M/D/YYYY
         *
         * Ví dụ:
         * 8/27/2026
         *
         * Tháng = 8
         * Ngày  = 27
         * Năm   = 2026
         *
         * => 2026-08-27
         */

        if (
            preg_match(
                '/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/',
                $value,
                $matches
            )
        ) {

            $month = (int) $matches[1];
            $day   = (int) $matches[2];
            $year  = (int) $matches[3];

            if (!checkdate($month, $day, $year)) {
                throw new \Exception(
                    "Ngày không hợp lệ: {$value}"
                );
            }

            return sprintf(
                '%04d-%02d-%02d',
                $year,
                $month,
                $day
            );
        }

        /*
         * D/M/YYYY
         *
         * Trường hợp Excel trả về:
         *
         * 27/8/2026
         *
         * => 2026-08-27
         */

        if (
            preg_match(
                '/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/',
                $value,
                $matches
            )
        ) {

            $day   = (int) $matches[1];
            $month = (int) $matches[2];
            $year  = (int) $matches[3];

            if (!checkdate($month, $day, $year)) {
                throw new \Exception(
                    "Ngày không hợp lệ: {$value}"
                );
            }

            return sprintf(
                '%04d-%02d-%02d',
                $year,
                $month,
                $day
            );
        }

        /*
         * YYYY-MM-DD
         */

        if (
            preg_match(
                '/^(\d{4})-(\d{1,2})-(\d{1,2})$/',
                $value,
                $matches
            )
        ) {

            $year  = (int) $matches[1];
            $month = (int) $matches[2];
            $day   = (int) $matches[3];

            if (!checkdate($month, $day, $year)) {
                throw new \Exception(
                    "Ngày không hợp lệ: {$value}"
                );
            }

            return sprintf(
                '%04d-%02d-%02d',
                $year,
                $month,
                $day
            );
        }

        throw new \Exception(
            'Không nhận diện được ngày: ' .
            var_export($value, true)
        );
    }
}