<?php

namespace Tests\Browser\Actions;

use Laravel\Dusk\Browser;
use Tests\Browser\Data\DanhMucKienNghi_KhoaHopData;
use Tests\Browser\Support\DuskLogger;
use Tests\Browser\Support\DuskNotify;

class ThemDanhMucKienNghi_KhoaHop
{
    public static function run(Browser $browser): void
    {
        DuskLogger::start('THÊM DANH MỤC KIẾN NGHỊ - KHÓA HỌP');

        $tong = 0;

        try {

            foreach (DanhMucKienNghi_KhoaHopData::danhSach() as $item) {

                $tong++;

                DuskLogger::info('Khóa họp: '.$item['name']);

                $browser
                    ->visit('https://hdnd.thainguyen.gov.vn/khoa-hop-kien-nghi/create')
                    ->pause(1000)

                    ->type('name', $item['name'])
                    ->select('nam', $item['nam']);

                if ($item['mo_ta'] !== '') {
                    $browser->type('mo_ta', $item['mo_ta']);
                }

                DuskLogger::info('Đã nhập thông tin');

                $browser->press('Lưu lại');

                DuskNotify::verify($browser);

                DuskLogger::info('Hoàn thành: '.$item['name']);
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