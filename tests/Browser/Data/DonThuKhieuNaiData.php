<?php

namespace Tests\Browser\Data;

use Tests\Browser\Reader\DonThuKhieuNaiReader;

class DonThuKhieuNaiData
{
    public static function danhSach(): array
    {
        $file = base_path('tests/Browser/Excel/DonThuKhieuNai.xlsx');

        return DonThuKhieuNaiReader::read($file);
    }
}