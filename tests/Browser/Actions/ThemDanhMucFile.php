<?php

namespace Tests\Browser\Actions;

use Laravel\Dusk\Browser;
use Tests\Browser\Data\DanhMucFileData;
use Tests\Browser\Support\DuskLogger;

class ThemDanhMucFile
{
    public static function run(Browser $browser): void
    {
        DuskLogger::start('THÊM DANH MỤC FILE');

        $tongBanGhi = 0;

        try {

            foreach (DanhMucFileData::danhSach() as $item) {

                $tongBanGhi++;

                DuskLogger::info('Danh mục: '.$item['name']);
                DuskLogger::info('Mô tả: '.$item['shortName']);

                $browser
                    ->visit('https://hdnd.thainguyen.gov.vn/danh-muc-file-cuoc-hop/create')
                    ->pause(800)

                    ->type('name', $item['name'])
                    ->type('shortName', $item['shortName']);

                DuskLogger::info('Đã nhập thông tin');

                $browser
                    ->press('Lưu lại')
                    ->pause(1200)
                    ->assertPathIs('/danh-muc-file-cuoc-hop');

                DuskLogger::info('Hoàn thành: '.$item['name']);
                DuskLogger::info(str_repeat('-', 50));
            }

            DuskLogger::info("ĐÃ HOÀN THÀNH {$tongBanGhi} BẢN GHI - KHÔNG GẶP LỖI");

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