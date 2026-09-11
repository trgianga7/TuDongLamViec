<?php

namespace Tests\Browser\Actions;

use Laravel\Dusk\Browser;
use Tests\Browser\Data\UserData;
use Tests\Browser\Support\DuskLogger;
use Tests\Browser\Support\DuskNotify;

class ThemNguoiDung
{
    public static function run(Browser $browser): void
    {
        DuskLogger::start('THÊM NGƯỜI DÙNG');

        $tongBanGhi = 0;

        try {

            foreach (UserData::danhSach() as $user) {

                $tongBanGhi++;

                DuskLogger::info('Người dùng: '.$user['name']);
                DuskLogger::info('Tài khoản: '.$user['username']);

                $browser
                    ->visit('https://hdnd.thainguyen.gov.vn/them-moi-nguoi-dung')
                    ->pause(1000)

                    ->type('name', $user['name'])
                    ->type('ngay_sinh', $user['ngay_sinh'])
                    ->type('username', $user['username'])
                    ->type('password', $user['password']);

                DuskLogger::info('Đã nhập thông tin cơ bản');

                $browser->select('role_id', $user['role_id']);

                DuskLogger::info('Đã chọn vai trò');

                $browser
                    ->type('phone', $user['phone'])
                    ->type('email', $user['email'])
                    ->type('chuc_vu_id', $user['chuc_vu_id']);

                DuskLogger::info('Đã nhập thông tin liên hệ');

                $browser
                    ->select('#don_vi_cap_1', $user['don_vi_id'])
                    ->pause(500);

                DuskLogger::info('Đã chọn đơn vị');

                //Vai trò hdnd
                $browser->select('vai_tro_hdnd', $user['vai_tro_hdnd']);
                DuskLogger::info('Đã chọn vai trò HĐND');

                if ($user['la_dai_bieu']) {
                    $browser->click('input[name="la_dai_bieu"] + .custom-switch-indicator');
                    DuskLogger::info('Đã bật: Đại biểu');
                }

                if ($user['tra_loi_chat_van']) {
                    $browser->click('input[name="tra_loi_chat_van"] + .custom-switch-indicator');
                    DuskLogger::info('Đã bật: Trả lời chất vấn');
                }

                if ($user['status']) {
                    $browser->click('input[name="status"] + .custom-switch-indicator');
                    DuskLogger::info('Đã bật: Hoạt động');
                }

                $browser->press('Lưu lại');

                DuskNotify::verify($browser);

                DuskLogger::info('Hoàn thành: '.$user['name']);
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