<?php

namespace Tests\Browser\Data;

use Tests\Browser\Reader\LichTrinhCuocHopReader;

class LichTrinhCuocHopData
{
    public static function danhSach(): array
    {
        return LichTrinhCuocHopReader::read(
            base_path('tests/Browser/Excel/LichTrinhCuocHop.xlsx')
        );
    }
}