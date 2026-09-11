<?php

namespace Tests\Browser\Actions;

use Laravel\Dusk\Browser;
use Tests\Browser\Data\KyHopData;
use Tests\Browser\Support\DuskLogger;

class ThemKyHop
{
    public static function run(Browser $browser): void
    {
        DuskLogger::start('THÊM KỲ HỌP');

        $tongBanGhi = 0;

        try {

            foreach (KyHopData::danhSach() as $kyHop) {

                $tongBanGhi++;

                DuskLogger::info('Kỳ họp: '.$kyHop['name']);
                DuskLogger::info('Khóa họp: '.$kyHop['khoa_hop_name']);

                $browser->visit('https://hdnd.thainguyen.gov.vn/ky-hop/create')
                    ->pause(1000);

                // Tìm value từ tên Khóa họp
                $value = $browser->script("
                    const select = document.querySelector('select[name=\"khoa_hop_id\"]');
                    const option = [...select.options].find(
                        o => o.text.trim() === '{$kyHop['khoa_hop_name']}'
                    );
                    return option ? option.value : '';
                ")[0];

                if ($value === '') {
                    throw new \Exception(
                        'Không tìm thấy Khóa họp: '.$kyHop['khoa_hop_name']
                    );
                }

                $browser
                    ->type('name', $kyHop['name'])
                    ->select('khoa_hop_id', $value);

                $browser->script("
                    const start = $('input[name=\"bat_dau\"]');
                    const end = $('input[name=\"ket_thuc\"]');

                    start.val('{$kyHop['bat_dau']}').trigger('change');
                    end.val('{$kyHop['ket_thuc']}').trigger('change');

                    start.datetimepicker('hide');
                    end.datetimepicker('hide');
                ");

                $browser
                    ->type('dia_diem_nhap', $kyHop['dia_diem_nhap'])
                    ->type('mo_ta', $kyHop['mo_ta'])
                    ->radio('type', $kyHop['type']);

                DuskLogger::info('Đã nhập thông tin');

                $browser
                    ->press('Lưu lại')
                    ->pause(1500)
                    ->assertPathIs('/ky-hop');

                DuskLogger::info('Hoàn thành: '.$kyHop['name']);
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