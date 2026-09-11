<?php

namespace Tests\Browser\Data;

use Tests\Browser\Reader\KetQuaGiamSatReader;

class KetQuaGiamSatData
{
    public static function danhSach(): array
    {
        $file = __DIR__ . '/../Excel/KetQuaGiamSat.xlsx';

        return KetQuaGiamSatReader::read($file);
    }
}