<?php

namespace Tests\Browser\Data;

use Tests\Browser\Reader\DanhMucKienNghi_KyHopReader;

class DanhMucKienNghi_KyHopData
{
    public static function danhSach(): array
    {
        $file = base_path(
            'tests/Browser/Excel/DanhMucKienNghi_KyHop.xlsx'
        );

        return DanhMucKienNghi_KyHopReader::read($file);
    }
}