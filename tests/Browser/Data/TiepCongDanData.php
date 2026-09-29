<?php

namespace Tests\Browser\Data;

use Tests\Browser\Reader\TiepCongDanReader;

class TiepCongDanData
{
    public static function danhSach(): array
    {
        return TiepCongDanReader::read(
            base_path('tests/Browser/Excel/TiepCongDan.xlsx')
        );
    }
}