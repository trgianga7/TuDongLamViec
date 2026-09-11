<?php

namespace Tests\Browser\Data;

use Tests\Browser\Reader\DanhMucFileReader;

class DanhMucFileData
{
    public static function danhSach(): array
    {
        $file = __DIR__ . '/../Excel/DanhMucFile.xlsx';

        return DanhMucFileReader::read($file);
    }
}