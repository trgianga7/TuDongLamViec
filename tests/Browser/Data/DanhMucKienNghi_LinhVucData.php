<?php

namespace Tests\Browser\Data;

use Tests\Browser\Reader\DanhMucKienNghi_LinhVucReader;

class DanhMucKienNghi_LinhVucData
{
    public static function danhSach(): array
    {
        $file = base_path(
            'tests/Browser/Excel/DanhMucKienNghi_LinhVuc.xlsx'
        );

        return DanhMucKienNghi_LinhVucReader::read($file);
    }
}