<?php

namespace Tests\Browser\Actions\QuyDauTuPT;

use Laravel\Dusk\Browser;
use Tests\Browser\Data\QuyDauTuData\DuAnDangThucHienData;
use Tests\Browser\Support\DuskLogger;

class ThemDuAnDangThucHien
{
    public static function run(Browser $browser): void
    {
        DuskLogger::start('THÊM DỰ ÁN ĐANG THỰC HIỆN');

        $tongBanGhi = 0;

        try {

            foreach (DuAnDangThucHienData::danhSach() as $duAn) {

                $tongBanGhi++;

                DuskLogger::info('Dự án: ' . $duAn['ten_du_an']);

                $browser->visit('https://hnfunds.vn/cap-nhat-du-an-dang-thuc-hien/create')
                    ->waitFor('input[name="ten_du_an"]', 10);

                $browser->type('ten_du_an', $duAn['ten_du_an'])
                    ->type('dia_chi', $duAn['dia_chi'])
                    ->select('doanh_nghiep_id', $duAn['doanh_nghiep_id'])
                    ->select('linh_vuc_cho_vay_id', $duAn['linh_vuc_cho_vay_id'])
                    ->type('thong_tin_chung', $duAn['thong_tin_chung'])
                    ->type('dai_dien_chu_dau_tu', $duAn['dai_dien_chu_dau_tu']);

                DuskLogger::info('Đã nhập thông tin chung');

                $browser->script("
                    document.querySelector(
                        'input[name=\"loai_du_an\"][value=\"{$duAn['loai_du_an']}\"]'
                    ).click();

                    document.querySelector(
                        'input[name=\"quy_che\"][value=\"{$duAn['quy_che']}\"]'
                    ).click();
                ");

                $browser->type('tong_muc_dau_tu', $duAn['tong_muc_dau_tu'])
                    ->type('tong_so_von_ky_hop_dong', $duAn['tong_so_von_ky_hop_dong'])
                    ->type('tong_du_no', $duAn['tong_du_no'])
                    ->type('so_con_giai_ngan', $duAn['so_con_giai_ngan']);

                $ngay = json_encode(
                    $duAn['ngay_tinh_tren_he_thong_moi'],
                    JSON_UNESCAPED_UNICODE
                );

                $browser->script("
                    const input = document.querySelector(
                        'input[name=\"ngay_tinh_tren_he_thong_moi\"]'
                    );

                    input.value = {$ngay};

                    ['input','change','blur'].forEach(evt => {
                        input.dispatchEvent(new Event(evt, { bubbles: true }));
                    });
                ");

                $browser->type('tong_goc_da_thu', $duAn['tong_goc_da_thu'])
                    ->type('tong_lai_da_thu', $duAn['tong_lai_da_thu'])
                    ->type('so_thang_vay', $duAn['so_thang_vay'])
                    ->type('lai_trong_han', $duAn['lai_trong_han'])
                    ->type('lai_qua_han', $duAn['lai_qua_han'])
                    ->type('lai_cham_tra', $duAn['lai_cham_tra'])
                    ->select('chu_ky_tra_goc', $duAn['chu_ky_tra_goc'])
                    ->select('chu_ky_tra_lai', $duAn['chu_ky_tra_lai']);

                DuskLogger::info('Đã nhập thông tin tài chính');

                foreach ($duAn['files'] as $file) {

                    $path = base_path('tests/Browser/File/'.$file);
                
                    if (! file_exists($path)) {
                        throw new \Exception("Không tìm thấy file: {$file}");
                    }
                
                    $browser
                        ->attach('input[name="ten_file[]"]', $path)
                        ->pause(500);
                
                    DuskLogger::info("Upload: {$file}");
                }

                if ($duAn['so_tai_khoan_khac'] !== '') {

                    $browser->type(
                        'so_tai_khoan_khac',
                        $duAn['so_tai_khoan_khac']
                    );
                }

                if ($duAn['ghi_chu'] !== '') {

                    $browser->type(
                        'ghi_chu',
                        $duAn['ghi_chu']
                    );
                }

                DuskLogger::info('Đã nhập ghi chú');

                $browser->press('Lưu lại')
                    ->pause(1500);

                DuskLogger::info('Hoàn thành: ' . $duAn['ten_du_an']);
                DuskLogger::info(str_repeat('-', 50));
            }

            DuskLogger::info("ĐÃ HOÀN THÀNH {$tongBanGhi} BẢN GHI - KHÔNG GẶP LỖI");

        } catch (\Throwable $e) {

            DuskLogger::info(str_repeat('=', 50));
            DuskLogger::info('HỆ THỐNG TOOL TỰ ĐỘNG GẶP LỖI!');
            DuskLogger::info('ĐÃ NGỪNG HỆ THỐNG!');
            DuskLogger::info("Bản ghi lỗi: {$tongBanGhi}");
            DuskLogger::info('Chi tiết: ' . $e->getMessage());
            DuskLogger::info(str_repeat('=', 50));

            throw $e;
        }
    }
}