<?php

namespace Tests\Browser\Support;

class DuskLogger
{
    protected static string $file = '';

    protected static function init(): void
    {
        if (self::$file === '') {
            self::$file = base_path('tests/Browser/logs/dusk.log');
        }
    }

    public static function start(string $job): void
    {
        self::init();

        file_put_contents(
            self::$file,
            "========== {$job} ==========\n".
            date('d/m/Y H:i:s')."\n\n"
        );
    }

    public static function info(string $text): void
    {
        self::init();

        file_put_contents(
            self::$file,
            '['.date('H:i:s')."] {$text}\n",
            FILE_APPEND
        );
    }
}