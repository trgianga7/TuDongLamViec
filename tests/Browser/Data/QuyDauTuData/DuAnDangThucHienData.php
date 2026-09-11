<?php

namespace Tests\Browser\Data\QuyDauTuData;

use Tests\Browser\Reader\QuyDauTuReader\DuAnDangThucHienReader;

class DuAnDangThucHienData
{
    public static function danhSach(): array
    {
        $file = base_path('tests/Browser/Excel/QuyDauTu/DuAnDangThucHien.xlsx');

        return DuAnDangThucHienReader::read($file);
    }
}