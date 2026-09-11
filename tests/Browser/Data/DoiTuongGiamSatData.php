<?php

namespace Tests\Browser\Data;

use Tests\Browser\Reader\DoiTuongGiamSatReader;

class DoiTuongGiamSatData
{
    public static function danhSach(): array
    {
        $file = __DIR__ . '/../Excel/DoiTuongGiamSat.xlsx';

        return DoiTuongGiamSatReader::read($file);
    }
}