<?php

namespace Tests\Browser\Data;

use Tests\Browser\Reader\TaiLieuBieuQuyetReader;

class TaiLieuBieuQuyetData
{
    public static function danhSach(): array
    {
        return TaiLieuBieuQuyetReader::read(
            __DIR__.'/../Excel/TaiLieuBieuQuyet.xlsx'
        );
    }
}