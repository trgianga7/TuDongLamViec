<?php

namespace Tests\Browser\Actions;

use Laravel\Dusk\Browser;
use Tests\Browser\Data\KienNghiCuTriData;
use Tests\Browser\Support\DuskLogger;
use Tests\Browser\Support\DuskNotify;

class ThemKienNghiCuTri
{
    public static function run(Browser $browser): void
    {
        DuskLogger::start('THÊM KIẾN NGHỊ CỬ TRI');

        $tongBanGhi = 0;

        try {

            foreach (KienNghiCuTriData::danhSach() as $item) {

                $tongBanGhi++;

                DuskLogger::info(
                    "Kiến nghị {$tongBanGhi}: ".$item['noi_dung']
                );

                // ================= Dashboard =================

                $browser->visit('https://hdnd.thainguyen.gov.vn/')
                    ->waitForLink('Quản lý kiến nghị cử tri')
                    ->clickLink('Quản lý kiến nghị cử tri');

                if ($item['loai_kien_nghi'] === 'quoc_hoi') {

                    DuskLogger::info('Loại: Quốc hội');

                    $browser->waitForLink('Kiến nghị cử tri Quốc hội')
                        ->clickLink('Kiến nghị cử tri Quốc hội')
                        ->waitForLink('Thêm mới')
                        ->clickLink('Thêm mới')
                        ->assertPathIs('/them-moi-kien-nghi-cu-tri-quoc-hoi');

                } else {

                    DuskLogger::info('Loại: HĐND');

                    $browser->waitForLink('Kiến nghị cử tri HDND')
                        ->clickLink('Kiến nghị cử tri HDND')
                        ->waitForLink('Thêm mới')
                        ->clickLink('Thêm mới')
                        ->assertPathIs('/them-moi-kien-nghi-cu-tri');
                }

                // ================= Form =================

                self::selectByText(
                    $browser,
                    'khoa_hop_id',
                    $item['khoa_hop']
                );

                self::selectByText(
                    $browser,
                    'ky_hop_id',
                    $item['ky_hop']
                );

                self::selectByText(
                    $browser,
                    'linh_vuc_id',
                    $item['linh_vuc']
                );

                self::selectByText(
                    $browser,
                    'phuong_xa_id',
                    $item['phuong_xa']
                );

                self::selectByText(
                    $browser,
                    'don_vi_id_giai_quyet',
                    $item['don_vi_giai_quyet']
                );

                self::selectByText(
                    $browser,
                    'iTrangThai',
                    $item['trang_thai']
                );

                $browser->type('xom', $item['xom'])
                    ->type('cu_tri', $item['cu_tri'])
                    ->type('noi_dung', $item['noi_dung']);

                DuskLogger::info('Đã nhập đầy đủ thông tin');

                // ================= File =================

                if (! empty($item['file_dinh_kem'])) {

                    $browser->type('txt_file[]', $item['ten_file'])
                        ->attach(
                            'ten_file[]',
                            base_path(
                                'tests/Browser/File/'.
                                $item['file_dinh_kem']
                            )
                        );

                    DuskLogger::info(
                        'Đính kèm: '.$item['file_dinh_kem']
                    );
                }

                // ================= Save =================

                $browser->press('Lưu lại');

                DuskNotify::verify($browser);

                DuskLogger::info("Hoàn thành bản ghi {$tongBanGhi}");
                DuskLogger::info(str_repeat('-', 50));
            }

            DuskLogger::info(
                "ĐÃ HOÀN THÀNH {$tongBanGhi} BẢN GHI"
            );

        } catch (\Throwable $e) {

            DuskLogger::info(str_repeat('=', 50));
            DuskLogger::info('HỆ THỐNG TOOL TỰ ĐỘNG GẶP LỖI');
            DuskLogger::info("Bản ghi lỗi: {$tongBanGhi}");
            DuskLogger::info('Chi tiết: '.$e->getMessage());
            DuskLogger::info(str_repeat('=', 50));

            throw $e;
        }
    }

    private static function selectByText(
        Browser $browser,
        string $name,
        string $text
    ): void {

        $browser->script("
            const select = document.querySelector('select[name=\"{$name}\"]');

            if(select){

                const target = ".json_encode($text).";

                [...select.options].forEach(o => {
                    if(o.text.trim() === target){
                        o.selected = true;
                    }
                });

                select.dispatchEvent(
                    new Event('change', { bubbles:true })
                );
            }
        ");

        $browser->pause(200);
    }
}