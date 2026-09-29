<?php

namespace Tests\Browser\Reader;

use PhpOffice\PhpSpreadsheet\IOFactory;

class TaiLieuBieuQuyetReader
{
    public static function read(string $path): array
    {
        $sheet = IOFactory::load($path)->getActiveSheet();
        $highestRow = $sheet->getHighestRow();

        $data = [];

        for ($row = 2; $row <= $highestRow; $row++) {

            $ten = trim((string)$sheet->getCell("A{$row}")->getValue());

            if ($ten === '') {
                continue;
            }

            $data[] = [
                'ten_cuoc_hop'  => $ten,
                'danh_muc'      => (string)$sheet->getCell("B{$row}")->getValue(),
                'so_ky_hieu'    => trim((string)$sheet->getCell("C{$row}")->getValue()),

                // Lấy đúng chuỗi đang hiển thị trong Excel
                'ngay_ban_hanh' => trim((string)$sheet->getCell("D{$row}")->getFormattedValue()),

                'tieu_de'       => trim((string)$sheet->getCell("E{$row}")->getValue()),
                'mo_ta'         => trim((string)$sheet->getCell("F{$row}")->getValue()),
                'file'          => trim((string)$sheet->getCell("G{$row}")->getValue()),
                'chu_tri_bieu_quyet' => trim((string)$sheet->getCell("H{$row}")->getValue()),
            ];
        }

        return $data;
    }
}