<?php

namespace Tests\Browser\Data;

use Tests\Browser\Reader\DiemDanhReader;

class DiemDanhData
{
    public static function danhSach(): array
    {
        return DiemDanhReader::read(
            __DIR__.'/../Excel/DiemDanh.xlsx'
        );
    }
}