<?php

namespace Tests\Browser\Actions;

use Facebook\WebDriver\WebDriverBy;
use Laravel\Dusk\Browser;
use Tests\Browser\Data\LichTrinhCuocHopData;
use Tests\Browser\Support\DuskLogger;

class ThemLichTrinhCuocHop
{
    public static function run(Browser $browser): void
    {
        DuskLogger::start('THÊM LỊCH TRÌNH CUỘC HỌP');

        $currentMeeting = null;
        $tongBanGhi = 0;

        try {

            foreach (LichTrinhCuocHopData::danhSach() as $item) {

                $tongBanGhi++;

                DuskLogger::info('Cuộc họp: '.$item['ten_cuoc_hop']);
                DuskLogger::info('Tiêu đề: '.$item['tieu_de']);

                // Chỉ mở lại khi đổi cuộc họp
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

                    // Mở màn hình Lịch trình cuộc họp
                    $browser->clickLink('Lịch trình cuộc họp')
                            ->pause(800)
                            ->waitFor('button[data-target="#modalAdd"]');

                    DuskLogger::info('Đã mở màn hình Lịch trình cuộc họp');
                }

                // Mở modal
                $browser->click(
                    'button[data-target="#modalAdd"]'
                )
                ->waitFor('#modalAdd')
                ->pause(300);

                // Nhập dữ liệu
                $browser->script("
                    const modal = document.querySelector('#modalAdd');

                    modal.querySelector('input[name=\"thoi_gian\"]').value =
                        ".json_encode($item['thoi_gian']).";

                    modal.querySelector('input[name=\"tieu_de\"]').value =
                        ".json_encode($item['tieu_de']).";

                    modal.querySelector('textarea[name=\"noi_dung\"]').value =
                        ".json_encode($item['noi_dung']).";

                    modal.querySelectorAll(
                        'input,textarea'
                    ).forEach(el=>{
                        el.dispatchEvent(
                            new Event('input',{bubbles:true})
                        );
                        el.dispatchEvent(
                            new Event('change',{bubbles:true})
                        );
                    });
                ");

                // Lưu trong modal
                $browser->script("
                    document.querySelector(
                        '#modalAdd .modal-footer .btn-success'
                    ).click();
                ");

                $browser->pause(1000);

                DuskLogger::info('Hoàn thành: '.$item['tieu_de']);
                DuskLogger::info(str_repeat('-',50));
            }

            DuskLogger::info("ĐÃ HOÀN THÀNH {$tongBanGhi} BẢN GHI - KHÔNG GẶP LỖI");

        } catch (\Throwable $e) {

            DuskLogger::info(str_repeat('=',50));
            DuskLogger::info('HỆ THỐNG TOOL TỰ ĐỘNG GẶP LỖI!');
            DuskLogger::info('ĐÃ NGỪNG HỆ THỐNG!');
            DuskLogger::info("Bản ghi lỗi: {$tongBanGhi}");
            DuskLogger::info('Chi tiết: '.$e->getMessage());
            DuskLogger::info(str_repeat('=',50));

            throw $e;
        }
    }
}