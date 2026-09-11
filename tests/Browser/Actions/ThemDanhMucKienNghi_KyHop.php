<?php

namespace Tests\Browser\Actions;

use Laravel\Dusk\Browser;
use Tests\Browser\Data\DanhMucKienNghi_KyHopData;
use Tests\Browser\Support\DuskLogger;
use Tests\Browser\Support\DuskNotify;

class ThemDanhMucKienNghi_KyHop
{
    public static function run(Browser $browser): void
    {
        DuskLogger::start('THÊM DANH MỤC KIẾN NGHỊ - KỲ HỌP');

        $tong = 0;

        try {

            foreach (DanhMucKienNghi_KyHopData::danhSach() as $item) {

                $tong++;

                DuskLogger::info('Kỳ họp: '.$item['name']);

                $browser
                    ->visit('https://hdnd.thainguyen.gov.vn/danh-muc-ky-hop-kien-nghi/create')
                    ->pause(1000)

                    ->type('name', $item['name']);

                $browser->script("
                    const setDate = (name, value) => {
                        const input = document.querySelector(`input[name=\"\${name}\"]`);
                        if (!input) return;

                        input.value = value;

                        ['input','change','blur'].forEach(evt=>{
                            input.dispatchEvent(new Event(evt,{bubbles:true}));
                        });
                    };

                    setDate('bat_dau', '{$item['bat_dau']}');
                    setDate('ket_thuc', '{$item['ket_thuc']}');
                ");

                if ($item['so_diem_tiep_xuc'] !== '') {
                    $browser->type('so_diem_tiep_xuc', $item['so_diem_tiep_xuc']);
                }

                if ($item['so_luong_cu_chi'] !== '') {
                    $browser->type('so_luong_cu_chi', $item['so_luong_cu_chi']);
                }

                DuskLogger::info('Đã nhập thông tin');

                $browser->press('Lưu lại');

                DuskNotify::verify($browser);

                DuskLogger::info('Hoàn thành: '.$item['name']);
                DuskLogger::info(str_repeat('-', 50));
            }

            DuskLogger::info(
                "ĐÃ HOÀN THÀNH {$tong} BẢN GHI - KHÔNG GẶP LỖI"
            );

        } catch (\Throwable $e) {

            DuskLogger::info(str_repeat('=', 50));
            DuskLogger::info('HỆ THỐNG TOOL TỰ ĐỘNG GẶP LỖI!');
            DuskLogger::info('ĐÃ NGỪNG HỆ THỐNG!');
            DuskLogger::info("Bản ghi lỗi: {$tong}");
            DuskLogger::info('Chi tiết: '.$e->getMessage());
            DuskLogger::info(str_repeat('=', 50));

            throw $e;
        }
    }
}