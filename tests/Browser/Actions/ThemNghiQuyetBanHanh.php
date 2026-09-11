<?php

namespace Tests\Browser\Actions;

use Facebook\WebDriver\WebDriverBy;
use Laravel\Dusk\Browser;
use Tests\Browser\Data\NghiQuyetBanHanhData;
use Tests\Browser\Support\DuskLogger;
use Tests\Browser\Support\DuskNotify;

class ThemNghiQuyetBanHanh
{
    public static function run(Browser $browser): void
    {
        DuskLogger::start('THÊM NGHỊ QUYẾT BAN HÀNH');

        $currentMeeting = null;
        $tongBanGhi = 0;

        try {

            foreach (NghiQuyetBanHanhData::danhSach() as $item) {

                $tongBanGhi++;

                DuskLogger::info('Cuộc họp: '.$item['ten_cuoc_hop']);
                DuskLogger::info('Số nghị quyết: '.$item['so_nghi_quyet']);

                // 1. Mở đúng cuộc họp
                if ($currentMeeting !== $item['ten_cuoc_hop']) {

                    $currentMeeting = $item['ten_cuoc_hop'];

                    $browser->visit('https://hdnd.thainguyen.gov.vn/lich-hop-da-tao')
                        ->waitFor('.meeting-item')
                        ->pause(500);

                    $items = $browser->elements('.meeting-item');
                    $found = false;

                    foreach ($items as $meeting) {

                        $title = trim(
                            $meeting->findElement(
                                WebDriverBy::cssSelector('.meeting-title')
                            )->getText()
                        );

                        if ($title === $currentMeeting) {

                            $meeting->findElement(
                                WebDriverBy::cssSelector('.meeting-title a')
                            )->click();

                            $found = true;
                            break;
                        }
                    }

                    if (!$found) {
                        throw new \Exception(
                            'Không tìm thấy cuộc họp: '.$currentMeeting
                        );
                    }

                    $browser->pause(800);

                    // 2. Mở panel Nghị quyết ban hành
                    $browser->script("
                        const link = document.querySelector('a[href=\"#xu-ly-nq\"]');

                        if (!link) {
                            throw new Error('Không tìm thấy panel nghị quyết');
                        }

                        if (!document.querySelector('#xu-ly-nq').classList.contains('in')) {
                            link.click();
                        }
                    ");

                    $browser->pause(400);

                    // 3. Click đúng nút Cập nhật trong panel nghị quyết
                    $browser->script("
                        const btn = document.querySelector('#xu-ly-nq a.btn.btn-danger.btn-sm');

                        if (!btn) {
                            throw new Error('Không tìm thấy nút cập nhật nghị quyết');
                        }

                        btn.click();
                    ");

                    $browser->pause(1000);

                    DuskLogger::info('Đã mở giao diện nghị quyết');
                }

                // 4. Nhập thông tin cơ bản
                $soNQ = json_encode($item['so_nghi_quyet'], JSON_UNESCAPED_UNICODE);
                $moTa = json_encode($item['mo_ta'], JSON_UNESCAPED_UNICODE);

                $browser->script("
                    const setInput = (name, value) => {
                        const input = document.querySelector('input[name=\"' + name + '\"]');

                        if (!input) throw new Error('Thiếu input: ' + name);

                        input.value = value;

                        ['input','change','blur'].forEach(evt => {
                            input.dispatchEvent(new Event(evt,{bubbles:true}));
                        });
                    };

                    setInput('so_nghi_quyet', {$soNQ});
                    setInput('mo_ta', {$moTa});
                    setInput('ngay_ban_hanh', '{$item['ngay_ban_hanh']}');
                    setInput('hieu_luc_tu', '{$item['hieu_luc_tu']}');
                    setInput('hieu_luc_den', '{$item['hieu_luc_den']}');
                ");

                DuskLogger::info('Đã nhập thông tin cơ bản');

                // 5. Nghị quyết thay thế
                if (!empty($item['thay_the'])) {

                    $browser->click('#btn-show-thay-the')
                        ->pause(400);

                    foreach ($item['thay_the'] as $i => $tt) {

                        if ($i > 0) {
                            $browser->click('.btn-add-thay-the')
                                ->pause(300);
                        }

                        $browser->script("
                            const rows = document.querySelectorAll('.nq-replace-item');
                            rows.forEach(r=>r.removeAttribute('id'));
                            rows[rows.length-1].id='current-replace';
                        ");

                        $so = json_encode($tt['so'], JSON_UNESCAPED_UNICODE);

                        $browser->script("
                            const select = document.querySelector('#current-replace select');
                            const target = {$so};

                            let ok = false;

                            [...select.options].forEach(opt=>{
                                if(opt.textContent.includes(target)){
                                    select.value = opt.value;
                                    ok = true;
                                }
                            });

                            if(!ok){
                                throw new Error('Không tìm thấy nghị quyết: '+target);
                            }

                            select.dispatchEvent(new Event('change',{bubbles:true}));
                        ");

                        $browser->type('#current-replace textarea', $tt['mo_ta']);

                        $browser->script("
                            const radio = document.querySelector(
                                '#current-replace input[value=\"{$tt['loai']}\"]'
                            );

                            if(radio) radio.click();
                        ");
                    }

                    DuskLogger::info(
                        'Đã thêm '.count($item['thay_the']).' nghị quyết thay thế'
                    );


                } else {

                    DuskLogger::info('Không có nghị quyết thay thế');
                }

                // 6. Upload file
                if (!empty($item['files'])) {

                    $paths = [];

                    foreach ($item['files'] as $file) {

                        $path = base_path('tests/Browser/File/'.$file);

                        if (!file_exists($path)) {
                            throw new \Exception('Không tìm thấy file: '.$path);
                        }

                        $paths[] = $path;
                    }

                    $browser->script("
                        document.querySelector('input[name=\"files[]\"]').id='nq-files';
                    ");

                    $browser->driver->findElement(
                        WebDriverBy::id('nq-files')
                    )->sendKeys(
                        implode("\n", $paths)
                    );

                    DuskLogger::info(
                        'Đã upload: '.implode(', ', $item['files'])
                    );

                } else {

                    DuskLogger::info('Không có file đính kèm');
                }

                // 7. Lưu
                $browser->press('Lưu nghị quyết');

                DuskNotify::verify($browser);

                DuskLogger::info('Hoàn thành: '.$item['so_nghi_quyet']);
                DuskLogger::info(str_repeat('-', 50));
            }

            DuskLogger::info(
                "ĐÃ HOÀN THÀNH {$tongBanGhi} BẢN GHI - KHÔNG GẶP LỖI"
            );

        } catch (\Throwable $e) {

            DuskLogger::info(str_repeat('=', 50));
            DuskLogger::info('HỆ THỐNG TOOL TỰ ĐỘNG GẶP LỖI!');
            DuskLogger::info('ĐÃ NGỪNG HỆ THỐNG!');
            DuskLogger::info("Bản ghi lỗi: {$tongBanGhi}");
            DuskLogger::info('Chi tiết: '.$e->getMessage());
            DuskLogger::info(str_repeat('=', 50));

            throw $e;
        }
    }
}