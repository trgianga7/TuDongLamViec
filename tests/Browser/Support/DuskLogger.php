<?php

namespace Tests\Browser\Support;

class DuskLogger
{
    protected static string $file = '';

    protected static function init(): void
    {
        if (self::$file === '') {

            self::$file = base_path('tests/Browser/logs/dusk.log');

            $dir = dirname(self::$file);

            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
        }
    }

    public static function start(string $job): void
    {
        self::init();

        $header =
            "========== {$job} ==========\n".
            date('d/m/Y H:i:s')."\n";

        file_put_contents(self::$file, $header."\n");

        self::stream($header);
    }

    public static function info(string $text): void
    {
        self::init();

        $line = '['.date('H:i:s')."] {$text}";

        file_put_contents(
            self::$file,
            $line.PHP_EOL,
            FILE_APPEND
        );

        self::stream($line);
    }

    protected static function stream(string $text): void
    {
        // Electron nhận realtime qua stderr
        fwrite(STDERR, $text.PHP_EOL);
        fflush(STDERR);
    }
}