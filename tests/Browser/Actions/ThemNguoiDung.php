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

                $browser->visit('https://hdnd.thainguyen.gov.vn/them-moi-nguoi-dung')
                    ->pause(1000)

                    ->type('name', $user['name'])
                    ->type('ngay_sinh', $user['ngay_sinh'])
                    ->type('username', $user['username'])
                    ->type('password', $user['password']);

                DuskLogger::info('Đã nhập thông tin cơ bản');

                // Vai trò
                self::selectByText($browser, 'role_id', $user['role']);
                DuskLogger::info('Đã chọn vai trò');

                // Liên hệ
                $browser->type('phone', $user['phone'])
                    ->type('email', $user['email'])
                    ->type('chuc_vu_id', $user['chuc_vu_id']);

                DuskLogger::info('Đã nhập thông tin liên hệ');

                // Đơn vị cấp 1
                self::selectById($browser, 'don_vi_cap_1', $user['don_vi']);
                DuskLogger::info('Đã chọn đơn vị cấp 1');

                // Đơn vị con (nếu có)
                if (!empty($user['don_vi_con'])) {

                    self::selectDonViCon($browser, $user['don_vi_con']);

                    DuskLogger::info('Đã chọn đơn vị con');
                }

                // Vai trò HĐND
                self::selectByText(
                    $browser,
                    'vai_tro_hdnd',
                    $user['vai_tro_hdnd']
                );

                DuskLogger::info('Đã chọn vai trò HĐND');

                // Switch
                if ($user['la_dai_bieu']) {
                    $browser->click(
                        'input[name="la_dai_bieu"] + .custom-switch-indicator'
                    );
                    DuskLogger::info('Đã bật: Đại biểu');
                }

                if ($user['tra_loi_chat_van']) {
                    $browser->click(
                        'input[name="tra_loi_chat_van"] + .custom-switch-indicator'
                    );
                    DuskLogger::info('Đã bật: Trả lời chất vấn');
                }

                if ($user['status']) {
                    $browser->click(
                        'input[name="status"] + .custom-switch-indicator'
                    );
                    DuskLogger::info('Đã bật: Hoạt động');
                }

                // Lưu
                $browser->press('Lưu lại');

                DuskNotify::verify($browser);

                DuskLogger::info('Hoàn thành: '.$user['name']);
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

    /**
     * Select theo text với select[name]
     */
    private static function selectByText(
        Browser $browser,
        string $name,
        string $text
    ): void {

        if (trim($text) === '') {
            return;
        }

        $browser->script("
            const select = document.querySelector(
                'select[name=\"{$name}\"]'
            );

            if(!select){
                throw new Error('Không tìm thấy select: {$name}');
            }

            const target = ".json_encode(trim($text)).";
            let found = false;

            [...select.options].forEach(option => {
                if(option.text.trim() === target){
                    option.selected = true;
                    found = true;
                }
            });

            if(!found){
                throw new Error('Không tìm thấy option: ' + target);
            }

            select.dispatchEvent(new Event('change',{bubbles:true}));

            if(window.jQuery){
                $(select).trigger('change');
            }
        ");

        $browser->pause(200);
    }

    /**
     * Select theo text với id
     */
    private static function selectById(
        Browser $browser,
        string $id,
        string $text
    ): void {

        if (trim($text) === '') {
            return;
        }

        $browser->script("
            const select = document.getElementById('{$id}');

            if(!select){
                throw new Error('Không tìm thấy select #{$id}');
            }

            const target = ".json_encode(trim($text)).";
            let found = false;

            [...select.options].forEach(option => {
                if(option.text.trim() === target){
                    option.selected = true;
                    found = true;
                }
            });

            if(!found){
                throw new Error('Không tìm thấy option: ' + target);
            }

            select.dispatchEvent(new Event('change',{bubbles:true}));

            if(window.jQuery){
                $(select).trigger('change');
            }
        ");

        $browser->pause(600);
    }

    /**
     * Chọn đơn vị con (.don-vi-child)
     */
    private static function selectDonViCon(
        Browser $browser,
        string $text
    ): void {

        $browser->waitFor('.don-vi-child', 5);

        $browser->script("
            const select = document.querySelector('.don-vi-child');

            if(!select){
                throw new Error('Không tìm thấy select đơn vị con');
            }

            const target = ".json_encode(trim($text)).";
            let value = null;

            [...select.options].forEach(option => {
                if(option.text.trim() === target){
                    option.selected = true;
                    value = option.value;
                }
            });

            if(value === null){
                throw new Error('Không tìm thấy đơn vị con: ' + target);
            }

            select.dispatchEvent(new Event('change',{bubbles:true}));

            if(window.jQuery){
                $(select).val(value).trigger('change');
            }

            const hidden = document.getElementById('don_vi_id');
            if(hidden){
                hidden.value = value;
                hidden.dispatchEvent(new Event('change',{bubbles:true}));
            }
        ");

        $browser->pause(500);
    }
}