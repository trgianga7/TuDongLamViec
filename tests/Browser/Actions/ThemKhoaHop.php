<?php

namespace Tests\Browser\Actions;

use Laravel\Dusk\Browser;
use Tests\Browser\Data\KhoaHopData;
use Tests\Browser\Support\DuskLogger;

class ThemKhoaHop
{
    public static function run(Browser $browser): void
    {
        DuskLogger::start('THÊM KHÓA HỌP');

        $tongBanGhi = 0;

        try {

            foreach (KhoaHopData::danhSach() as $khoaHop) {

                $tongBanGhi++;

                DuskLogger::info('Khóa họp: '.$khoaHop['name']);
                DuskLogger::info('Năm: '.$khoaHop['nam']);
                DuskLogger::info('Mô tả: '.$khoaHop['mo_ta']);

                $browser
                    ->visit('https://hdnd.thainguyen.gov.vn/khoa-hop/create')
                    ->pause(1000)

                    ->type('name', $khoaHop['name'])
                    ->select('nam', $khoaHop['nam'])
                    ->type('mo_ta', $khoaHop['mo_ta']);

                DuskLogger::info('Đã nhập thông tin');

                $browser
                    ->press('Lưu lại')
                    ->pause(1500)
                    ->assertPathIs('/khoa-hop');

                DuskLogger::info('Hoàn thành: '.$khoaHop['name']);
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