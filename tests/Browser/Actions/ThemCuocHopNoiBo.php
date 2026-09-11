<?php

namespace Tests\Browser\Actions;

use Laravel\Dusk\Browser;
use Tests\Browser\Data\CuocHopNoiBoData;
use Tests\Browser\Support\DuskLogger;
use Tests\Browser\Support\DuskNotify;

class ThemCuocHopNoiBo
{
    public static function run(Browser $browser): void
    {
        DuskLogger::start('THÊM CUỘC HỌP NỘI BỘ');

        $tongBanGhi = 0;

        try {

            foreach (CuocHopNoiBoData::danhSach() as $hop) {

                $tongBanGhi++;

                DuskLogger::info('Tên cuộc họp: ' . $hop['title']);

                $browser->visit('https://hdnd.thainguyen.gov.vn/cuoc-hop-noi-bo/tao-moi')
                    ->waitFor('input[name="title"]', 10)
                    ->pause(500);

                // Thông tin cuộc họp
                $browser->type('title', $hop['title'])
                    ->select('meeting_qr_code_id', (string) $hop['meeting_qr_code_id']);

                // Thời gian
                $browser->script("
                    $('#started_at_picker').data('DateTimePicker')
                        .date(moment('{$hop['started_at']}', 'DD/MM/YYYY HH:mm'));

                    $('#ended_at_picker').data('DateTimePicker')
                        .date(moment('{$hop['ended_at']}', 'DD/MM/YYYY HH:mm'));
                ");

                $browser->pause(300);

                DuskLogger::info('Đã nhập thông tin cuộc họp');

                // Tài liệu cuộc họp
                if (!empty($hop['documents'])) {

                    foreach ($hop['documents'] as $doc) {

                        $browser->click('#add-document')
                            ->pause(300);

                        $browser->script("
                            const rows = document.querySelectorAll('.document-row');
                            rows.forEach(r => r.removeAttribute('id'));
                            rows[rows.length - 1].id = 'current-document';
                        ");

                        $browser->type(
                            '#current-document input[type=text]',
                            $doc['name']
                        );

                        if (!empty($doc['file'])) {

                            $path = base_path('tests/Browser/File/' . $doc['file']);

                            if (!file_exists($path)) {
                                throw new \Exception('Không tìm thấy file: ' . $path);
                            }

                            $browser->attach(
                                '#current-document input[type=file]',
                                $path
                            );
                        }

                        DuskLogger::info(
                            'Đã thêm tài liệu: ' .
                            $doc['name'] . ' - ' . $doc['file']
                        );
                    }

                } else {

                    DuskLogger::info('Không có tài liệu');
                }

                // Ghi chú
                if (!empty($hop['note'])) {

                    $browser->type('note', $hop['note']);

                    DuskLogger::info('Đã nhập ghi chú');

                } else {

                    DuskLogger::info('Không có ghi chú');
                }

                // Lưu
                $browser->press('Lưu cuộc họp');

                DuskNotify::verify($browser);

                DuskLogger::info('Hoàn thành: ' . $hop['title']);
                DuskLogger::info(str_repeat('-', 50));
            }

            DuskLogger::info("ĐÃ HOÀN THÀNH {$tongBanGhi} BẢN GHI - KHÔNG GẶP LỖI");

        } catch (\Throwable $e) {

            DuskLogger::info(str_repeat('=', 50));
            DuskLogger::info('HỆ THỐNG TOOL TỰ ĐỘNG GẶP LỖI!');
            DuskLogger::info('ĐÃ NGỪNG HỆ THỐNG!');
            DuskLogger::info("Bản ghi lỗi: {$tongBanGhi}");
            DuskLogger::info('Chi tiết: ' . $e->getMessage());
            DuskLogger::info(str_repeat('=', 50));

            throw $e;
        }
    }
}