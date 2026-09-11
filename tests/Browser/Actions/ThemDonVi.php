<?php

namespace Tests\Browser\Actions;

use Laravel\Dusk\Browser;
use Tests\Browser\Data\DonViData;
use Tests\Browser\Support\DuskLogger;

class ThemDonVi
{
    public static function run(Browser $browser): void
    {
        DuskLogger::start('THÊM ĐƠN VỊ');

        $tongBanGhi = 0;

        try {

            foreach (DonViData::danhSach() as $donVi) {

                $tongBanGhi++;

                DuskLogger::info('Đơn vị: '.$donVi['ten_don_vi']);

                $browser
                    ->visit('https://hdnd.thainguyen.gov.vn/don-vi/create')
                    ->pause(1000)
                    ->type('ten_don_vi', $donVi['ten_don_vi']);

                // Đơn vị chủ quản
                if ($donVi['parent_name'] !== '') {

                    $value = $browser->script("
                        const select = document.querySelector('select[name=\"parent_id\"]');
                        const option = [...select.options].find(
                            o => o.text.trim() === '{$donVi['parent_name']}'
                        );
                        return option ? option.value : '';
                    ")[0];

                    if ($value === '') {
                        throw new \Exception(
                            'Không tìm thấy đơn vị chủ quản: '.$donVi['parent_name']
                        );
                    }

                    $browser->select('parent_id', $value);

                    DuskLogger::info('Đơn vị chủ quản: '.$donVi['parent_name']);
                } else {
                    DuskLogger::info('Không có đơn vị chủ quản');
                }

                $browser
                    ->type('ten_viet_tat', $donVi['ten_viet_tat'])
                    ->type('ma_hanh_chinh', $donVi['ma_hanh_chinh'])
                    ->type('dia_chi', $donVi['dia_chi'])
                    ->type('dien_thoai', $donVi['dien_thoai'])
                    ->type('email', $donVi['email']);

                DuskLogger::info('Đã nhập thông tin cơ bản');

                // Cấp tổ chức
                $browser->script("
                    const radio = document.querySelector(
                        'input[name=\"cap_to_chuc\"][value=\"{$donVi['cap_to_chuc']}\"]'
                    );

                    if (!radio) {
                        throw new Error('Không tìm thấy cap_to_chuc');
                    }

                    const helper = radio.parentElement.querySelector('.iCheck-helper');

                    if (helper) helper.click();
                    else radio.click();
                ");

                $browser->pause(300);

                DuskLogger::info('Cấp tổ chức: '.$donVi['cap_to_chuc']);

                // Điều hành
                if ($donVi['cap_to_chuc'] !== '1') {

                    $browser->script("
                        const radio = document.querySelector(
                            'input[name=\"dieu_hanh\"][value=\"{$donVi['dieu_hanh']}\"]'
                        );

                        if (!radio) {
                            throw new Error('Không tìm thấy dieu_hanh');
                        }

                        const helper = radio.parentElement.querySelector('.iCheck-helper');

                        if (helper) helper.click();
                        else radio.click();
                    ");

                    $browser->pause(300);

                    DuskLogger::info('Điều hành: '.$donVi['dieu_hanh']);
                }

                // Lưu
                $browser
                    ->press('Lưu thông tin')
                    ->pause(1500)
                    ->assertPathIs('/don-vi');

                DuskLogger::info('Hoàn thành: '.$donVi['ten_don_vi']);
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