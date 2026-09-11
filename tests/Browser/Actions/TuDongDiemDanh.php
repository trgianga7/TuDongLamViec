<?php

namespace Tests\Browser\Actions;

use Exception;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Exception\NoSuchAlertException;
use Laravel\Dusk\Browser;
use Tests\Browser\Data\DiemDanhData;
use Tests\Browser\Support\DuskLogger;

class TuDongDiemDanh
{
    public static function run(Browser $browser): void
    {
        DuskLogger::start('ĐIỂM DANH HÀNG LOẠT');

        $currentMeeting = null;
        $tongBanGhi = 0;

        try {

            foreach (DiemDanhData::danhSach() as $item) {

                $tongBanGhi++;

                if ($currentMeeting !== $item['ten_cuoc_hop']) {

                    $currentMeeting = $item['ten_cuoc_hop'];

                    DuskLogger::info('Cuộc họp: ' . $currentMeeting);

                    $browser->visit('https://hdnd.thainguyen.gov.vn/lich-hop-da-tao')
                        ->waitFor('.meeting-item', 15)
                        ->pause(800);

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

                    if (! $found) {
                        throw new Exception(
                            'Không tìm thấy cuộc họp: ' . $currentMeeting
                        );
                    }

                    $browser->pause(1000);

                    $browser->script("
                        const links = [...document.querySelectorAll('a')];

                        const btn = links.find(x =>
                            x.href.includes('/man-hinh-phong-hop/')
                        );

                        if(btn){
                            btn.id = 'btn-phong-hop';
                        }
                    ");

                    $browser->click('#btn-phong-hop')
                        ->waitFor('.checkin-row', 15)
                        ->pause(1000);

                    DuskLogger::info('Đã mở màn hình phòng họp');
                }

                DuskLogger::info('Điểm danh: ' . $item['ten_dai_bieu']);

                $ten = addslashes($item['ten_dai_bieu']);

                $result = $browser->script("
                    let success = false;

                    document.querySelectorAll('.checkin-box').forEach(box => {

                        if(success) return;

                        const name = box.querySelector('.checkin-seat-name')?.innerText.trim();

                        if(name === '{$ten}'){

                            const btn = box.querySelector('.btn-checkin-thanh-vien');

                            if(btn){
                                btn.click();
                                success = true;
                            }
                        }
                    });

                    return success;
                ");

                if ($result[0]) {

                    usleep(500000);

                    try {
                        $browser->driver->switchTo()->alert()->accept();
                    } catch (NoSuchAlertException $e) {
                    }

                    $browser->pause(300);

                    DuskLogger::info('✓ Điểm danh thành công');

                } else {

                    DuskLogger::info('✗ Không tìm thấy hoặc đã điểm danh');
                }

                DuskLogger::info(str_repeat('-', 50));
            }

            DuskLogger::info("ĐÃ HOÀN THÀNH {$tongBanGhi} LƯỢT ĐIỂM DANH");

        } catch (\Throwable $e) {

            DuskLogger::info(str_repeat('=', 50));
            DuskLogger::info('HỆ THỐNG ĐIỂM DANH GẶP LỖI');
            DuskLogger::info("Bản ghi lỗi: {$tongBanGhi}");
            DuskLogger::info('Chi tiết: ' . $e->getMessage());
            DuskLogger::info(str_repeat('=', 50));

            throw $e;
        }
    }
}