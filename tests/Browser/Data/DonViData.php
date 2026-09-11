<?php

namespace Tests\Browser\Data;

use Tests\Browser\Reader\DonViReader;

class DonViData
{
    public static function danhSach(): array
    {
        $file = __DIR__ . '/../Excel/DonVi.xlsx';

        return DonViReader::read($file);
    }
}