<?php

namespace Tests\Browser\Data;

use Tests\Browser\Reader\GiamSatReader;

class GiamSatData
{
    public static function danhSach(): array
    {
        $file = __DIR__.'/../Excel/GiamSat.xlsx';

        return GiamSatReader::read($file);
    }
}