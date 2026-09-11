<?php

namespace Tests\Browser\Actions;

use Laravel\Dusk\Browser;
use Tests\Browser\Data\DoiTuongGiamSatData;
use Tests\Browser\Support\DuskLogger;

class ThemDoiTuongGiamSat
{
    public static function run(Browser $browser): void
    {
        DuskLogger::start('THÊM ĐỐI TƯỢNG GIÁM SÁT');

        $tongBanGhi = 0;

        try {

            foreach (DoiTuongGiamSatData::danhSach() as $doiTuong) {

                $tongBanGhi++;

                DuskLogger::info('Tên: ' . $doiTuong['name']);

                $browser->visit('https://hdnd.thainguyen.gov.vn/doi-tuong-giam-sat/create')
                    ->pause(1000)
                    ->type('name', $doiTuong['name']);

                if ($doiTuong['shortName'] !== '') {
                    $browser->type('shortName', $doiTuong['shortName']);
                    DuskLogger::info('Đã nhập mô tả');
                } else {
                    DuskLogger::info('Không có mô tả');
                }

                $browser->press('Lưu lại')
                    ->pause(2000);

                DuskLogger::info('Hoàn thành: ' . $doiTuong['name']);
            }

            DuskLogger::info("ĐÃ HOÀN THÀNH {$tongBanGhi} BẢN GHI");

        } catch (\Throwable $e) {

            DuskLogger::info('HỆ THỐNG TOOL TỰ ĐỘNG GẶP LỖI');
            DuskLogger::info("Bản ghi lỗi: {$tongBanGhi}");
            DuskLogger::info('Chi tiết: ' . $e->getMessage());

            throw $e;
        }
    }
}