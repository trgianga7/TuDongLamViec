<?php

namespace Tests\Browser\Reader;

use PhpOffice\PhpSpreadsheet\IOFactory;
use DateTime;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class TiepCongDanReader
{
    public static function read(string $path): array
    {
        $sheet = IOFactory::load($path)->getActiveSheet();
        $rows = $sheet->toArray(null,true,true,false);

        $header = array_shift($rows);
        $data = [];

        foreach ($rows as $index => $row) {

            if (trim((string)$row[0]) === '') continue;

            $excelRow = $index + 2;
            $item = array_combine($header,$row);

            $thanhPhan = self::tach($item['thanh_phan'] ?? '');
            $chucVu = self::tach($item['chuc_vu'] ?? '');

            $hoSoTen = self::tach($item['ho_so_ten'] ?? '');
            $hoSoFile = self::tach($item['ho_so_file'] ?? '');

            $ketQuaTen = self::tach($item['ket_qua_ten'] ?? '');
            $ketQuaFile = self::tach($item['ket_qua_file'] ?? '');

            $hoSo = [];
            foreach ($hoSoTen as $i => $ten) {
                $hoSo[] = [
                    'ten'=>$ten,
                    'file'=>$hoSoFile[$i] ?? ''
                ];
            }

            $ketQua = [];
            foreach ($ketQuaTen as $i => $ten) {
                $ketQua[] = [
                    'ten'=>$ten,
                    'file'=>$ketQuaFile[$i] ?? ''
                ];
            }

            $data[] = [
                'phan_loai' => $item['phan_loai'],
                'loai'=>$item['loai'],
                'chu_tri'=>$item['chu_tri'],
                'chu_tri_chuc_vu'=>$item['chu_tri_chuc_vu'],
                'ngay_tiep'=>self::formatDate($sheet->getCell("E{$excelRow}")),
                'loai_don'=>$item['loai_don'],

                'thanh_phan'=>$thanhPhan,
                'chuc_vu'=>$chucVu,

                'ho_ten_cong_dan'=>$item['ho_ten_cong_dan'],
                'cmt'=>$item['cmt'],
                'dia_chi'=>$item['dia_chi'],

                'ho_so'=>$hoSo,

                'noi_dung_tiep'=>$item['noi_dung_tiep'],
                'ket_luan'=>$item['ket_luan'],
                'ket_qua_thuc_hien'=>$item['ket_qua_thuc_hien'],

                'ket_qua'=>$ketQua,
            ];
        }

        return $data;
    }

    private static function formatDate($cell): string
    {
        $value = $cell->getValue();

        if (is_numeric($value) && Date::isDateTime($cell)) {
            return Date::excelToDateTimeObject($value)->format('d/m/Y');
        }

        $value = trim((string)$value);

        foreach (['d/m/Y','n/j/Y','Y-m-d'] as $f) {
            $dt = DateTime::createFromFormat($f,$value);
            if ($dt) return $dt->format('d/m/Y');
        }

        return $value;
    }

    private static function tach(string $text): array
    {
        if (trim($text) === '') return [];

        return array_values(array_filter(
            array_map('trim', explode('|',$text)),
            fn($v)=>$v!==''
        ));
    }
}