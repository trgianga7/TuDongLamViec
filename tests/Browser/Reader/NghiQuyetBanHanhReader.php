<?php

namespace Tests\Browser\Reader;

use DateTime;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class NghiQuyetBanHanhReader
{
    public static function read(string $path): array
    {
        $sheet = IOFactory::load($path)->getActiveSheet();

        $rows = $sheet->toArray(null,true,true,false);

        $header = array_shift($rows);

        $data = [];

        foreach ($rows as $index=>$row){

            if(trim((string)$row[0])===''){
                continue;
            }

            $excelRow = $index + 2;

            $item = array_combine($header,$row);

            // Nghị quyết thay thế
            $nghiQuyetCu = self::tach($item['nghi_quyet_cu'] ?? '');
            $moTaThayThe = self::tach($item['mo_ta_thay_the'] ?? '');
            $loaiThayThe = self::tach($item['loai_thay_the'] ?? '');

            $thayThe = [];

            foreach ($nghiQuyetCu as $i => $so) {

                $thayThe[] = [
                    'so'    => $so,
                    'mo_ta' => $moTaThayThe[$i] ?? '',
                    'loai'  => $loaiThayThe[$i] ?? 'mot_phan',
                ];
            }

            $data[]=[

                'ten_cuoc_hop'=>trim($item['ten_cuoc_hop']),

                'so_nghi_quyet'=>trim($item['so_nghi_quyet']),

                'ngay_ban_hanh'=>self::formatDate(
                    $sheet->getCell("C{$excelRow}")
                ),

                'hieu_luc_tu'=>self::formatDate(
                    $sheet->getCell("D{$excelRow}")
                ),

                'hieu_luc_den'=>self::formatDate(
                    $sheet->getCell("E{$excelRow}")
                ),

                'mo_ta'=>trim($item['mo_ta']),

                'thay_the' => $thayThe,

                'files'=>self::tach($item['files'] ?? ''),
            ];
        }

        return $data;
    }

    private static function formatDate($cell): string //Có thể bỏ trống
    {
        $value = $cell->getValue();

        if ($value === null || trim((string)$value) === '') {
            return '';
        }

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
        if(trim($text)===''){
            return [];
        }

        return array_values(
            array_filter(
                array_map('trim',explode('|',$text)),
                fn($v)=>$v!==''
            )
        );
    }
}