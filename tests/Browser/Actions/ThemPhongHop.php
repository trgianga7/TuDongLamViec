<?php

namespace Tests\Browser\Actions;

use Laravel\Dusk\Browser;
use Tests\Browser\Data\PhongHopData;
use Tests\Browser\Support\DuskLogger;

class ThemPhongHop
{
    public static function run(Browser $browser): void
    {
        DuskLogger::start('THÊM PHÒNG HỌP');

        $tongBanGhi = 0;

        try {

            foreach (PhongHopData::danhSach() as $phongHop) {

                $tongBanGhi++;

                DuskLogger::info('Phòng họp: '.$phongHop['name']);
                DuskLogger::info('Mô tả: '.$phongHop['shortName']);

                $browser
                    ->visit('https://hdnd.thainguyen.gov.vn/phong-hop/create')
                    ->pause(500)

                    ->type('name', $phongHop['name'])
                    ->type('shortName', $phongHop['shortName']);

                DuskLogger::info('Đã nhập thông tin');

                $browser
                    ->press('Lưu lại')
                    ->pause(1500)
                    ->assertPathIs('/phong-hop');

                DuskLogger::info('Hoàn thành: '.$phongHop['name']);
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