<?php

namespace Tests\Browser\Actions;

use Laravel\Dusk\Browser;
use Tests\Browser\Data\ChuTheData;
use Tests\Browser\Support\DuskLogger;

class ThemChuThe
{
    public static function run(Browser $browser): void
    {
        DuskLogger::start('THÊM CHỦ THỂ');

        $tongBanGhi = 0;

        try {

            foreach (ChuTheData::danhSach() as $chuThe) {

                $tongBanGhi++;

                DuskLogger::info('Tên chủ thể: ' . $chuThe['name']);

                $browser->visit('https://hdnd.thainguyen.gov.vn/chu-the/create')
                    ->waitFor('input[name="name"]', 10)
                    ->pause(500);

                // Tên chủ thể
                $browser->type('name', $chuThe['name']);

                // Mô tả (có thể bỏ trống)
                if (!empty($chuThe['short_name'])) {

                    $browser->type('shortName', $chuThe['short_name']);

                    DuskLogger::info('Đã nhập mô tả');

                } else {

                    DuskLogger::info('Không có mô tả');
                }

                $browser->press('Lưu lại')
                    ->pause(1500);

                DuskLogger::info('Hoàn thành: ' . $chuThe['name']);
                DuskLogger::info(str_repeat('-', 50));
            }

            DuskLogger::info("ĐÃ HOÀN THÀNH {$tongBanGhi} BẢN GHI - KHÔNG GẶP LỖI");

        } catch (\Throwable $e) {

            DuskLogger::info('HỆ THỐNG TOOL TỰ ĐỘNG GẶP LỖI!');
            DuskLogger::info('ĐÃ NGỪNG HỆ THỐNG!');
            DuskLogger::info("Bản ghi lỗi: {$tongBanGhi}");
            DuskLogger::info('Chi tiết: ' . $e->getMessage());

            throw $e;
        }
    }
}