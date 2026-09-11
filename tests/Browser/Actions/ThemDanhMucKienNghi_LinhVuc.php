<?php

namespace Tests\Browser\Actions;

use Laravel\Dusk\Browser;
use Tests\Browser\Data\DanhMucKienNghi_LinhVucData;
use Tests\Browser\Support\DuskLogger;
use Tests\Browser\Support\DuskNotify;

class ThemDanhMucKienNghi_LinhVuc
{
    public static function run(Browser $browser): void
    {
        DuskLogger::start('THÊM DANH MỤC KIẾN NGHỊ - LĨNH VỰC');

        $tong = 0;

        try {

            foreach (DanhMucKienNghi_LinhVucData::danhSach() as $lv) {

                $tong++;

                DuskLogger::info('Lĩnh vực: '.$lv['ten']);

                $browser
                    ->visit('https://hdnd.thainguyen.gov.vn/linh-vuc')
                    ->pause(1000)

                    // Mở form collapse
                    ->click('button[data-toggle="collapse"][href="#collapseExample"]')
                    ->pause(500)

                    // Nhập dữ liệu
                    ->type('ten', $lv['ten'])
                    ->select('nhom_linh_vuc', $lv['nhom_linh_vuc']);

                if ($lv['mo_ta'] !== '') {
                    $browser->type('mo_ta', $lv['mo_ta']);
                }

                DuskLogger::info('Đã nhập thông tin');

                $browser->press('Lưu lại');

                DuskNotify::verify($browser);

                DuskLogger::info('Hoàn thành: '.$lv['ten']);
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