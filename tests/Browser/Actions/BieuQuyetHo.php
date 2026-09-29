<?php

namespace Tests\Browser\Actions;

use Facebook\WebDriver\WebDriverBy;
use Laravel\Dusk\Browser;
use Tests\Browser\Data\BieuQuyetHoData;
use Tests\Browser\Support\DuskLogger;

class BieuQuyetHo
{
    public static function run(Browser $browser): void
    {
        DuskLogger::start('BIỂU QUYẾT HỘ');

        $currentMeeting = null;
        $tongBanGhi = 0;

        try {

            foreach (BieuQuyetHoData::danhSach() as $item) {

                $tongBanGhi++;

                DuskLogger::info('Cuộc họp: '.$item['ten_cuoc_hop']);
                DuskLogger::info('Thành viên: '.$item['thanh_vien']);
                DuskLogger::info('Kết quả: '.$item['ket_qua']);

                // Mở cuộc họp
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

                    if (! $found) {
                        throw new \Exception(
                            'Không tìm thấy cuộc họp: '.$currentMeeting
                        );
                    }

                    $browser->pause(800)
                        ->clickLink('Biểu quyết hộ')
                        ->waitFor('#proxy-member-list')
                        ->pause(500);

                    DuskLogger::info('Đã mở màn hình Biểu quyết hộ');
                }

                // Chọn thành viên
                $browser->script("
                    const target = ".json_encode($item['thanh_vien']).";

                    const btn = [...document.querySelectorAll('.proxy-member-item')]
                        .find(x => x.dataset.userName.trim() === target);

                    if(!btn){
                        throw new Error('Không tìm thấy thành viên: ' + target);
                    }

                    btn.click();
                ");

                $browser->pause(300);

                $selected = $browser->script("
                    const btn = document.querySelector('.proxy-member-item.selected');
                    return btn ? btn.dataset.userName : '';
                ")[0];

                if ($selected !== $item['thanh_vien']) {
                    throw new \Exception('Chọn sai thành viên: '.$selected);
                }

                DuskLogger::info('Đã chọn: '.$selected);

                // Chuyển text sang type
                $type = match ($item['ket_qua']) {
                    'Tán thành'       => 1,
                    'Không tán thành' => 2,
                    'Không ý kiến'    => 3,
                    default => throw new \Exception(
                        'Kết quả không hợp lệ: '.$item['ket_qua']
                    ),
                };

                // Biểu quyết
                $browser->script("
                    const btn = document.querySelector(
                        '.proxy-vote-btn[data-type=\"{$type}\"]'
                    );

                    if(!btn){
                        throw new Error('Không tìm thấy nút biểu quyết');
                    }

                    btn.click();
                ");

                // Xác nhận Yes
                $browser->waitForDialog(3)
                ->acceptDialog();

                // Chờ gửi xong
                $browser->waitUntilMissing('#proxy-sending', 5);

                // Chờ trạng thái đổi
                $browser->waitUsing(
                5,
                100,
                function () use ($browser, $item) {

                    return $browser->script("
                        const btn = [...document.querySelectorAll('.proxy-member-item')]
                            .find(x => x.dataset.userName.trim() === ".json_encode($item['thanh_vien']).");

                        if(!btn) return false;

                        const status = btn.querySelector('.proxy-member-status');

                        if(!status) return false;

                        return status.textContent.trim() !== 'Chưa biểu quyết';
                    ")[0];
                },
                'Thành viên chưa chuyển sang trạng thái đã biểu quyết.'
                );

                DuskLogger::info('Đã biểu quyết thành công');
                DuskLogger::info('Hoàn thành: '.$item['thanh_vien']);
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