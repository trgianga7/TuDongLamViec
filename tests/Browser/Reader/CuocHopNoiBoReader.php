<?php

namespace Tests\Browser\Reader;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class CuocHopNoiBoReader
{
    public static function read(string $path): array
    {
        $sheet = IOFactory::load($path)->getActiveSheet();

        $rows = $sheet->toArray(null, true, true, false);

        $header = array_shift($rows);

        $data = [];

        foreach ($rows as $index => $row) {

            if (trim((string) $row[0]) === '') {
                continue;
            }

            $item = array_combine($header, $row);

            $names = self::tach($item['document_names'] ?? '');
            $files = self::tach($item['document_files'] ?? '');

            $documents = [];

            $max = max(count($names), count($files));

            for ($i = 0; $i < $max; $i++) {

                if (($names[$i] ?? '') === '' && ($files[$i] ?? '') === '') {
                    continue;
                }

                $documents[] = [
                    'name' => $names[$i] ?? '',
                    'file' => $files[$i] ?? '',
                ];
            }

            $excelRow = $index + 2;

            $data[] = [
                'title' => trim($item['title']),
                'meeting_qr_code_id' => trim($item['meeting_qr_code_id']),
                'started_at' => self::formatDate($sheet->getCell("C{$excelRow}")),
                'ended_at' => self::formatDate($sheet->getCell("D{$excelRow}")),
                'note' => trim($item['note']),
                'documents' => $documents,
            ];
        }

        return $data;
    }

    private static function formatDate($cell): string
    {
        $value = $cell->getValue();

        // Ô Date thực của Excel
        if (is_numeric($value) && Date::isDateTime($cell)) {
            return Date::excelToDateTimeObject($value)
                ->format('d/m/Y H:i');
        }

        $value = trim((string) $value);

        $formats = [
            'd/m/Y H:i',
            'd/m/Y G:i',
            'd/m/Y H:i:s',
            'd/m/Y G:i:s',
            'n/j/Y G:i',
            'n/j/Y H:i',
            'm/d/Y H:i',
            'Y-m-d H:i:s',
        ];

        foreach ($formats as $format) {
            $dt = \DateTime::createFromFormat($format, $value);
            if ($dt !== false) {
                return $dt->format('d/m/Y H:i');
            }
        }

        return $value;
    }

    private static function tach(string $text): array
    {
        if (trim($text) === '') {
            return [];
        }

        return array_map('trim', explode('|', $text));
    }
}