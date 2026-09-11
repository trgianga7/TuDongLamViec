<?php

namespace Tests\Browser\Data;

use Tests\Browser\Reader\ChuTheReader;

class ChuTheData
{
    public static function danhSach(): array
    {
        $file = __DIR__ . '/../Excel/ChuThe.xlsx';

        return ChuTheReader::read($file);
    }
}