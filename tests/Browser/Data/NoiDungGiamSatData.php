<?php

namespace Tests\Browser\Data;

use Tests\Browser\Reader\NoiDungGiamSatReader;

class NoiDungGiamSatData
{
    public static function danhSach(): array
    {
        $file = __DIR__ . '/../Excel/NoiDungGiamSat.xlsx';

        return NoiDungGiamSatReader::read($file);
    }
}