<?php

namespace Tests\Browser\Data;

use Tests\Browser\Reader\PhieuLayYKienReader;

class PhieuLayYKienData
{
    public static function danhSach(): array
    {
        return PhieuLayYKienReader::read(
            base_path('tests/Browser/Excel/PhieuLayYKien.xlsx')
        );
    }
}