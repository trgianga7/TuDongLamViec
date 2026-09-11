<?php

namespace Tests\Browser\Data;

use Tests\Browser\Reader\CuocHopNoiBoReader;

class CuocHopNoiBoData
{
    public static function danhSach(): array
    {
        return CuocHopNoiBoReader::read(
            base_path('tests/Browser/Excel/CuocHopNoiBo.xlsx')
        );
    }
}