<?php

namespace Tests\Browser\Data;

use Tests\Browser\Reader\NghiQuyetBanHanhReader;

class NghiQuyetBanHanhData
{
    public static function danhSach(): array
    {
        $file = base_path('tests/Browser/Excel/NghiQuyetBanHanh.xlsx');

        return NghiQuyetBanHanhReader::read($file);
    }
}