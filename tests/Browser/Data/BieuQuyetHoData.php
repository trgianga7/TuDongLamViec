<?php

namespace Tests\Browser\Data;

use Tests\Browser\Reader\BieuQuyetHoReader;

class BieuQuyetHoData
{
    public static function danhSach(): array
    {
        return BieuQuyetHoReader::read(
            base_path('tests/Browser/Excel/BieuQuyetHo.xlsx')
        );
    }
}