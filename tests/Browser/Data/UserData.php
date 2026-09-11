<?php

namespace Tests\Browser\Data;

use Tests\Browser\Reader\UserReader;

class UserData
{
    public static function danhSach(): array
    {
        $file = __DIR__ . '/../Excel/NguoiDung.xlsx';

        return UserReader::read($file);
    }

}