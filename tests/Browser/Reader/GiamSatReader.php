<?php

namespace Tests\Browser\Reader;

use DateTime;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class GiamSatReader
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

            // ===== Quyết định =====
            $tenQD  = self::tach($item['quyet_dinh_ten'] ?? '');
            $fileQD = self::tach($item['quyet_dinh_file'] ?? '');

            $quyetDinh = [];

            foreach ($tenQD as $i => $ten) {
                $quyetDinh[] = [
                    'ten'  => $ten,
                    'file' => $fileQD[$i] ?? '',
                ];
            }

            // ===== Tài liệu =====
            $danhMuc = self::tach($item['tai_lieu_danh_muc'] ?? '');
            $files   = self::tach($item['tai_lieu_file'] ?? '');

            $taiLieu = [];

            foreach ($danhMuc as $i => $dm) {

                $list = [];

                if (!empty($files[$i])) {
                    $list = array_map(
                        'trim',
                        explode(',', $files[$i])
                    );
                }

                $taiLieu[] = [
                    'danh_muc' => $dm,
                    'files'    => $list,
                ];
            }

            $data[] = [
                'loai_tt' => trim($item['loai_tt']),
                'chu_the' => trim($item['chu_the']),
                'hinh_thuc' => trim($item['hinh_thuc']),
                'noi_dung' => trim($item['noi_dung']),

                'bat_dau' =>
                    self::formatDate($sheet->getCell("G{$excelRow}")),

                'ket_thuc' =>
                    self::formatDate($sheet->getCell("H{$excelRow}")),

                'doi_tuong_truc_tiep' =>
                    self::tach($item['doi_tuong_truc_tiep'] ?? ''),

                'doi_tuong_gian_tiep' =>
                    self::tach($item['doi_tuong_gian_tiep'] ?? ''),

                'quyet_dinh' => $quyetDinh,
                'tai_lieu'   => $taiLieu,
            ];
        }

        return $data;
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

    private static function tach(string $text): array
    {
        if (trim($text) === '') {
            return [];
        }

        return array_values(
            array_filter(
                array_map('trim', explode('|', $text))
            )
        );
    }
}