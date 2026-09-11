<?php

namespace Tests\Browser\Data;

use Tests\Browser\Reader\PhongHopReader;

class PhongHopData
{
    public static function danhSach(): array
    {
        $file = __DIR__ . '/../Excel/PhongHop.xlsx';

        return PhongHopReader::read($file);
    }
}