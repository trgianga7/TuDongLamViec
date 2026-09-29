<?php

namespace Tests\Browser\Actions;

use Laravel\Dusk\Browser;
use Tests\Browser\Data\TiepCongDanData;
use Tests\Browser\Support\DuskLogger;
use Tests\Browser\Support\DuskNotify;

class ThemTiepCongDan
{
    public static function run(Browser $browser): void
    {
        DuskLogger::start('THÊM TIẾP CÔNG DÂN');

        $tongBanGhi = 0;

        try {

            foreach (TiepCongDanData::danhSach() as $item) {

                $tongBanGhi++;

                DuskLogger::info('Phân loại: '.$item['phan_loai']);
                DuskLogger::info('Loại: '.$item['loai']);
                DuskLogger::info('Công dân: '.$item['ho_ten_cong_dan']);

                // Mở dashboard
                $browser->visit('https://hdnd.thainguyen.gov.vn/')
                    ->waitFor('a[href*="don-thu-khieu-nai-dashboad"]')
                    ->click('a[href*="don-thu-khieu-nai-dashboad"]')
                    ->pause(600);

                // Chọn menu
                $phanLoai = mb_strtolower(trim($item['phan_loai']));
                $loai = mb_strtolower(trim($item['loai']));

                if ($phanLoai === 'quốc hội') {

                    if ($loai === 'định kỳ') {
                        $browser->click(
                            'a[href*="them-moi-tiep-cong-dan-dinh-ky-quoc-hoi"]'
                        );
                    } else {
                        $browser->click(
                            'a[href*="them-moi-tiep-cong-dan-dot-xuat-quoc-hoi"]'
                        );
                    }

                } else {

                    if ($loai === 'định kỳ') {
                        $browser->click(
                            'a[href*="them-moi-tiep-cong-dan-dinh-ky"]'
                        );
                    } else {
                        $browser->click(
                            'a[href*="tiep-cong-dan-dot-xuat/create"]'
                        );
                    }
                }

                $browser->waitFor('input[name="chu_tri"]')
                    ->pause(300);

                // Thông tin lãnh đạo
                $browser->type('input[name="chu_tri"]', $item['chu_tri'])
                    ->type('input[name="chu_tri_chuc_vu"]', $item['chu_tri_chuc_vu']);

                // Ngày tiếp
                $browser->script("
                    const input = document.querySelector('input[name=\"ngay_tiep\"]');

                    input.value = ".json_encode($item['ngay_tiep']).";

                    ['input','change','blur'].forEach(evt=>{
                        input.dispatchEvent(new Event(evt,{bubbles:true}));
                    });

                    if(window.jQuery && $(input).datepicker){
                        $(input).datepicker('hide');
                    }

                    document.body.click();
                ");

                $browser->pause(300)
                    ->type('loai_don', $item['loai_don']);

                DuskLogger::info('Đã nhập thông tin lãnh đạo');

                // Thành phần tham gia
                foreach ($item['thanh_phan'] as $i => $tp) {

                    if ($i > 0) {

                        $browser->script("
                            document.querySelector('.btn-add').click();
                        ");

                        $browser->pause(300);
                    }

                    $browser->script("
                        const rows=document.querySelectorAll('.thanhphan-item');
                        rows.forEach(r=>r.removeAttribute('id'));
                        rows[rows.length-1].id='current-row';
                    ");

                    $browser->type(
                        '#current-row input[name="thanh_phan[]"]',
                        $tp
                    );

                    $browser->type(
                        '#current-row input[name="chuc_vu[]"]',
                        $item['chuc_vu'][$i] ?? ''
                    );
                }

                DuskLogger::info(
                    'Đã thêm '.count($item['thanh_phan']).' thành phần'
                );

                // Thông tin công dân
                $browser->type(
                    'ho_ten_cong_dan',
                    $item['ho_ten_cong_dan']
                )
                ->type('cmt', $item['cmt'])
                ->type('dia_chi', $item['dia_chi']);

                // Hồ sơ
                foreach ($item['ho_so'] as $i => $hs) {

                    if ($i > 0) {

                        $browser->script("
                            document.querySelector(
                                '.increment-ho-so .btn-add-file'
                            ).click();
                        ");

                        $browser->pause(350);
                    }

                    $browser->script("
                        const rows = document.querySelectorAll('.increment-ho-so .row');
                        rows.forEach(r=>r.removeAttribute('id'));

                        const current = rows[rows.length-1];
                        current.id = 'hoso-row';

                        document.querySelectorAll(
                            '.increment-ho-so input[type=file]'
                        ).forEach(f=>f.removeAttribute('id'));

                        current.querySelector(
                            'input[type=file]'
                        ).id='hoso-file';
                    ");

                    $browser->type(
                        '#hoso-row input[name="txt_file_ho_so[]"]',
                        $hs['ten']
                    );

                    $path = base_path(
                        'tests/Browser/File/'.$hs['file']
                    );

                    if (!file_exists($path)) {
                        throw new \Exception(
                            'Không tìm thấy file: '.$path
                        );
                    }

                    $browser->attach('#hoso-file', $path);

                    DuskLogger::info(
                        'Đã thêm hồ sơ: '.$hs['ten']
                    );
                }

                // Nội dung
                $browser->type(
                    'noi_dung_tiep',
                    $item['noi_dung_tiep']
                )
                ->type('ket_luan', $item['ket_luan'])
                ->type(
                    'ket_qua_thuc_hien',
                    $item['ket_qua_thuc_hien']
                );

                // File kết quả
                foreach ($item['ket_qua'] as $i => $kq) {

                    if ($i > 0) {

                        $browser->script("
                            document.querySelector(
                                '.increment-ket-qua .btn-add-file'
                            ).click();
                        ");

                        $browser->pause(350);
                    }

                    $browser->script("
                        const rows = document.querySelectorAll('.increment-ket-qua .row');
                        rows.forEach(r=>r.removeAttribute('id'));

                        const current = rows[rows.length-1];
                        current.id = 'kq-row';

                        document.querySelectorAll(
                            '.increment-ket-qua input[type=file]'
                        ).forEach(f=>f.removeAttribute('id'));

                        current.querySelector(
                            'input[type=file]'
                        ).id='kq-file';
                    ");

                    $browser->type(
                        '#kq-row input[name="txt_file_ket_qua[]"]',
                        $kq['ten']
                    );

                    $path = base_path(
                        'tests/Browser/File/'.$kq['file']
                    );

                    if (!file_exists($path)) {
                        throw new \Exception(
                            'Không tìm thấy file: '.$path
                        );
                    }

                    $browser->attach('#kq-file', $path);

                    DuskLogger::info(
                        'Đã thêm kết quả: '.$kq['ten']
                    );
                }

                // Lưu
                $browser->press('Lưu lại');

                DuskNotify::verify($browser);

                DuskLogger::info(
                    'Hoàn thành: '.$item['ho_ten_cong_dan']
                );

                DuskLogger::info(str_repeat('-',50));
            }

            DuskLogger::info(
                "ĐÃ HOÀN THÀNH {$tongBanGhi} BẢN GHI - KHÔNG GẶP LỖI"
            );

        } catch (\Throwable $e) {

            DuskLogger::info(str_repeat('=',50));
            DuskLogger::info('HỆ THỐNG TOOL TỰ ĐỘNG GẶP LỖI!');
            DuskLogger::info('ĐÃ NGỪNG HỆ THỐNG!');
            DuskLogger::info("Bản ghi lỗi: {$tongBanGhi}");
            DuskLogger::info('Chi tiết: '.$e->getMessage());
            DuskLogger::info(str_repeat('=',50));

            throw $e;
        }
    }
}