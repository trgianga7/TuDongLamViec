<?php

namespace Tests\Browser\Data;

use Tests\Browser\Reader\CuocHopReader;

class CuocHopData
{
    public static function danhSach(): array
    {
        $file = base_path('tests/Browser/Excel/CuocHop.xlsx');

        return CuocHopReader::read($file);
    }
}