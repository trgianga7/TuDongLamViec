<?php

namespace Tests\Browser\Data;

use Tests\Browser\Reader\DanhMucKienNghi_KhoaHopReader;

class DanhMucKienNghi_KhoaHopData
{
    public static function danhSach(): array
    {
        $file = base_path(
            'tests/Browser/Excel/DanhMucKienNghi_KhoaHop.xlsx'
        );

        return DanhMucKienNghi_KhoaHopReader::read($file);
    }
}