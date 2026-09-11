<?php

namespace Tests\Browser\Actions;

use Facebook\WebDriver\WebDriverBy;
use Laravel\Dusk\Browser;
use Tests\Browser\Data\PhieuLayYKienData;
use Tests\Browser\Support\DuskLogger;

class ThemPhieuLayYKien
{
    public static function run(Browser $browser): void
    {
        DuskLogger::start('THÊM PHIẾU LẤY Ý KIẾN');

        $tong = 0;

        try {

            foreach (PhieuLayYKienData::danhSach() as $p) {

                $tong++;

                DuskLogger::info('Số văn bản: '.$p['so_van_ban']);

                $browser->visit('https://hdnd.thainguyen.gov.vn/phieu-lay-y-kien-theo-mau/create')
                    ->pause(1000);

                // Thông tin chung
                $browser->type('the_thuc1', $p['the_thuc1'])
                    ->type('the_thuc2', $p['the_thuc2'])
                    ->type('the_thuc3', $p['the_thuc3'])
                    ->type('so_van_ban', $p['so_van_ban'])
                    ->type('trich_yeu_ngan', $p['trich_yeu_ngan'])
                    ->type('kinh_gui', $p['kinh_gui'])
                    ->type('noi_dung_nghien_cuu', $p['noi_dung_nghien_cuu'])
                    ->type('can_cu', $p['can_cu'])
                    ->type('ngay', $p['ngay'])
                    ->type('thang', $p['thang'])
                    ->type('nam', $p['nam']);

                DuskLogger::info('Đã nhập thông tin chung');

                // Biểu quyết
                foreach ($p['bieu_quyet'] as $i => $item) {

                    $browser->click('button[onclick="addItem()"]')
                        ->pause(300);

                    // Gán ID động cho textarea
                    $browser->script("
                        const areas = document.querySelectorAll('textarea[name*=\"[noi_dung]\"]');
                        areas.forEach(a => a.removeAttribute('id'));
                        areas[$i].id = 'current-noi-dung';
                    ");

                    $browser->type('#current-noi-dung', $item['noi_dung']);

                    if (!empty($item['files'])) {

                        // Gán ID động cho input file
                        $browser->script("
                            const inputs = document.querySelectorAll('input[name*=\"[files][]\"]');
                            inputs.forEach(i => i.removeAttribute('id'));
                            inputs[$i].id = 'current-bieu-quyet-file';
                        ");

                        $paths = [];

                        foreach ($item['files'] as $file) {

                            $path = base_path('tests/Browser/File/'.$file);

                            if (!file_exists($path)) {
                                throw new \Exception("Không tìm thấy file: $path");
                            }

                            $paths[] = $path;
                        }

                        $browser->driver->findElement(
                            WebDriverBy::id('current-bieu-quyet-file')
                        )->sendKeys(implode("\n", $paths));

                        DuskLogger::info(
                            'Đã thêm biểu quyết '.($i + 1).' - '.implode(', ', $item['files'])
                        );

                    } else {

                        DuskLogger::info(
                            'Đã thêm biểu quyết '.($i + 1).' - Không có file'
                        );
                    }
                }

                // Hạn trả lời
                $browser->type('ngay_ket_thuc', $p['han_tra_loi'])
                    ->type('gio_ket_thuc_tai_tai_lieu', $p['gio_tra_loi'])
                    ->select('type', $p['loai_phieu']);

                DuskLogger::info('Đã nhập hạn trả lời');

                // Tài liệu chung
                if (!empty($p['tai_lieu_chung'])) {

                    foreach ($p['tai_lieu_chung'] as $i => $tl) {

                        if ($i > 0) {
                            $browser->click('#addFileChung')
                                ->pause(300);
                        }

                        // Gán ID động
                        $browser->script("
                            const rows = document.querySelectorAll('.file-chung-row');

                            rows.forEach(r => {
                                r.removeAttribute('id');
                            });

                            rows[$i].id = 'current-file-row';
                        ");

                        $browser->type(
                            '#current-file-row input[name="file_chung_names[]"]',
                            $tl['ten']
                        );

                        if ($tl['file'] !== '') {

                            $path = base_path('tests/Browser/File/'.$tl['file']);

                            if (!file_exists($path)) {
                                throw new \Exception("Không tìm thấy file: $path");
                            }

                            $browser->attach(
                                '#current-file-row input[type=file]',
                                $path
                            );
                        }

                        DuskLogger::info(
                            'Đã thêm tài liệu chung: '.$tl['ten'].' - '.$tl['file']
                        );
                    }

                } else {

                    DuskLogger::info('Không có tài liệu chung');
                }

                // Lưu
                $browser->press('Lưu phiếu')
                    ->pause(2000);

                DuskLogger::info('Hoàn thành: '.$p['so_van_ban']);
                DuskLogger::info(str_repeat('-', 50));
            }

            DuskLogger::info(
                "ĐÃ HOÀN THÀNH {$tong} BẢN GHI - KHÔNG GẶP LỖI"
            );

        } catch (\Throwable $e) {

            DuskLogger::info(str_repeat('=', 50));
            DuskLogger::info('HỆ THỐNG TOOL TỰ ĐỘNG GẶP LỖI!');
            DuskLogger::info('ĐÃ NGỪNG HỆ THỐNG!');
            DuskLogger::info("Bản ghi lỗi: {$tong}");
            DuskLogger::info('Chi tiết: '.$e->getMessage());
            DuskLogger::info(str_repeat('=', 50));

            throw $e;
        }
    }
}