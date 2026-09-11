<?php

namespace Tests\Browser\Data;

use Tests\Browser\Reader\KyHopReader;

class KyHopData
{
    public static function danhSach(): array
    {
        $file = __DIR__ . '/../Excel/KyHop.xlsx';

        return KyHopReader::read($file);
    }
}