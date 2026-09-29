<?php

namespace Tests\Browser\Data;

use Tests\Browser\Reader\KienNghiCuTriReader;

class KienNghiCuTriData
{
    public static function danhSach(): array
    {
        $file = base_path('tests/Browser/Excel/KienNghiCuTri.xlsx');

        return KienNghiCuTriReader::read($file);
    }
}