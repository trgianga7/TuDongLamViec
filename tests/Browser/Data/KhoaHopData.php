<?php

namespace Tests\Browser\Data;

use Tests\Browser\Reader\KhoaHopReader;

class KhoaHopData
{
    public static function danhSach(): array
    {
        $file = __DIR__ . '/../Excel/KhoaHop.xlsx';

        return KhoaHopReader::read($file);
    }
}