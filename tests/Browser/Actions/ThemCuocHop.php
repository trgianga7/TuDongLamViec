<?php

namespace Tests\Browser\Actions;

use Laravel\Dusk\Browser;
use Tests\Browser\Data\CuocHopData;
use Tests\Browser\Support\DuskLogger;
use Tests\Browser\Support\DuskNotify;

class ThemCuocHop
{
    public static function run(Browser $browser): void
    {
        DuskLogger::start('THÊM CUỘC HỌP');

        $tongBanGhi = 0;

        try {

            foreach (CuocHopData::danhSach() as $item) {

                $tongBanGhi++;

                DuskLogger::info('Cuộc họp: '.$item['tieu_de']);

                $browser->visit('https://hdnd.thainguyen.gov.vn/cuoc-hop/create')
                    ->pause(1000)

                    ->type('tieu_de', $item['tieu_de'])
                    ->type('noi_dung', $item['noi_dung'])

                    ->select('khoa_hop_id', $item['khoa_hop_id'])
                    ->pause(800)

                    ->select('ky_hop_id', $item['ky_hop_id'])

                    ->select('phong_hop', $item['phong_hop'])

                    ->type('dia_diem', $item['dia_diem'])

                    ->type('gio', $item['gio'])
                    ->type('gio_ket_thuc', $item['gio_ket_thuc'])
                    ->type(
                        'gio_ket_thuc_tai_tai_lieu',
                        $item['gio_ket_thuc_tai_tai_lieu']
                    );

                $browser->script("
                    const setDate = (field, value) => {
                        const input = document.querySelector(`input[name=\"\${field}\"]`);
                        if (!input) return;
                
                        input.value = value;
                
                        ['input','change','blur'].forEach(evt => {
                            input.dispatchEvent(new Event(evt, { bubbles: true }));
                        });
                    };
                
                    setDate('ngay', '{$item['ngay']}');
                    setDate('ngay_ket_thuc', '{$item['ngay_ket_thuc']}');
                    setDate(
                        'ngay_ket_thuc_tai_tai_lieu',
                        '{$item['ngay_ket_thuc_tai_tai_lieu']}'
                    );
                
                    document.querySelector(
                        'input[name=\"thong_bao_ket_luan\"][value=\"{$item['thong_bao_ket_luan']}\"]'
                    ).click();
                
                    document.querySelector(
                        'input[name=\"phien_hop\"][value=\"{$item['phien_hop']}\"]'
                    ).click();
                ");

                DuskLogger::info('Đã nhập đầy đủ thông tin');

                $browser->press('Lưu lại');

                DuskNotify::verify($browser);

                DuskLogger::info('Hoàn thành: '.$item['tieu_de']);
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
}