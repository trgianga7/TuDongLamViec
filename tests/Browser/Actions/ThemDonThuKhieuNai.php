<?php

namespace Tests\Browser\Actions;

use Laravel\Dusk\Browser;
use Tests\Browser\Data\DonThuKhieuNaiData;
use Tests\Browser\Support\DuskLogger;
use Tests\Browser\Support\DuskNotify;

class ThemDonThuKhieuNai
{
    public static function run(Browser $browser): void
    {
        DuskLogger::start('THÊM ĐƠN THƯ');

        $tong = 0;

        foreach (DonThuKhieuNaiData::danhSach() as $don) {

            $tong++;

            $browser->visit('https://hdnd.thainguyen.gov.vn/')
                ->waitFor('a[href*="don-thu-khieu-nai-dashboad"]')
                ->click('a[href*="don-thu-khieu-nai-dashboad"]')
                ->pause(1000);

            if ($don['cap_don'] === 'Quốc hội') {

                $browser->click(
                    'a[href*="them-moi-don-thu-khieu-nai-quoc-hoi"]'
                );

            } else {

                $browser->click(
                    'a[href*="them-moi-don-thu-khieu-nai"]'
                );
            }

            $browser->waitFor('input[name="so_thu_tu"]');

            // ===== Tiếp nhận =====
            $browser->type('so_thu_tu', $don['so_thu_tu']);

            $browser->script("
                document.querySelector('input[name=\"ngay_nhap_don\"]').value='{$don['ngay_nhap_don']}';
            ");

            self::selectByText($browser, 'tinh_trang_xu_ly',
                $don['tinh_trang_xu_ly']);

            self::selectByText($browser, 'don_doc',
                $don['don_doc']);

            self::selectByText($browser, 'don_luu',
                $don['don_luu']);

            self::check($browser, 'don_trung', $don['don_trung']);
            self::check($browser, 'don_khong_du_dieu_kien',
                $don['don_khong_du_dieu_kien']);
            self::check($browser, 'don_du_dieu_kien',
                $don['don_du_dieu_kien']);

            // ===== Người gửi =====
            self::selectByText($browser, 'doi_tuong_gui_don',
                $don['doi_tuong_gui_don']);

            self::selectByText($browser, 'thuoc_to_chuc',
                $don['thuoc_to_chuc']);

            $browser->type('ho_ten_chu_don', $don['ho_ten_chu_don'])
                ->type('cmt', $don['cmt'])
                ->type('sdt_email', $don['sdt_email']);

            self::selectByText($browser, 'phuong_xa_id',
                $don['phuong_xa']);

            $browser->type('dia_chi', $don['dia_chi']);

            self::selectByText($browser, 'nghe_nghiep',
                $don['nghe_nghiep']);

            self::selectByText($browser, 'nguon_don',
                $don['nguon_don']);

            self::selectByText($browser, 'loai_don',
                $don['loai_don']);

            self::selectByText($browser, 'linh_vuc_don',
                $don['linh_vuc_don']);

            // ===== Bị khiếu nại =====
            self::selectByText($browser,
                'doi_tuong_bi_khieu_nai',
                $don['doi_tuong_bi_khieu_nai']);

            $browser->type('ho_ten_bi_khieu_nai',
                    $don['ho_ten_bi_khieu_nai'])
                ->type('ten_co_quan', $don['ten_co_quan'])
                ->type('dia_chi_bi_khieu_nai',
                    $don['dia_chi_bi_khieu_nai']);

            self::selectByText($browser,
                'nghe_nghiep_bi_khieu_nai',
                $don['nghe_nghiep_bi_khieu_nai']);

            // ===== Lãnh đạo =====
            self::selectByText($browser,
                'noi_nhan_don',
                $don['noi_nhan_don']);

            $browser->type('lanh_dao_chi_dao_id',
                $don['lanh_dao_chi_dao']);

            self::selectByText($browser,
                'chuyen_vien_xu_ly_id',
                $don['chuyen_vien_xu_ly']);

            self::check(
                $browser,
                'coquanphoihopxuly',
                $don['coquanphoihopxuly']
            );

            self::check(
                $browser,
                'coquanphoihopxuly1',
                $don['coquanphoihopxuly1']
            );

            self::check(
                $browser,
                'coquanphoihopxuly2',
                $don['coquanphoihopxuly2']
            );

            self::check(
                $browser,
                'coquanphoihopxuly3',
                $don['coquanphoihopxuly3']
            );

            self::check(
                $browser,
                'coquanphoihopxuly4',
                $don['coquanphoihopxuly4']
            );

            // ===== Hồ sơ =====
            foreach ($don['ho_so'] as $i => $hs) {

                if ($i > 0) {
                    $browser->click('.themnhieu')
                        ->pause(300);
                }

                $browser->script("
                    const rows=document.querySelectorAll('.item-ho-so');
                    rows.forEach(r=>r.removeAttribute('id'));
                    rows[rows.length-1].id='hoso';
                ");

                if ($hs['file'] !== '') {

                    $browser->attach(
                        '#hoso input[type=file]',
                        base_path('tests/Browser/File/'.$hs['file'])
                    );
                }

                $browser->type(
                    '#hoso textarea',
                    $hs['noi_dung']
                );
            }

            $browser->press('Lưu lại');

            DuskNotify::verify($browser);

            DuskLogger::info('Hoàn thành: '.$don['ho_ten_chu_don']);
        }

        DuskLogger::info("ĐÃ HOÀN THÀNH {$tong} BẢN GHI");
    }

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
    
            if (!select) {
                throw new Error(
                    'Không tìm thấy select[name=\"{$name}\"]'
                );
            }
    
            const target = ".json_encode(trim($text)).";
    
            let found = false;
    
            [...select.options].forEach(option => {
    
                if (option.text.trim() === target) {
    
                    option.selected = true;
                    found = true;
                }
            });
    
            if (!found) {
                throw new Error(
                    'Không tìm thấy option \"' + target +
                    '\" trong select[name=\"{$name}\"]'
                );
            }
    
            select.dispatchEvent(
                new Event('change', { bubbles: true })
            );
    
            if (window.jQuery) {
                $(select).trigger('change');
            }
        ");
    
        $browser->pause(200);
    }

    private static function check(
        Browser $browser,
        string $name,
        bool $checked
    ): void {
    
        $browser->script("
            const checkbox = document.querySelector(
                'input[type=\"checkbox\"][name=\"{$name}\"]'
            );
    
            if (!checkbox) {
                throw new Error(
                    'Không tìm thấy checkbox[name=\"{$name}\"]'
                );
            }
    
            checkbox.checked = ".($checked ? 'true' : 'false').";
    
            checkbox.dispatchEvent(
                new Event('change', { bubbles: true })
            );
    
            checkbox.dispatchEvent(
                new Event('input', { bubbles: true })
            );
        ");
    
        $browser->pause(100);
    }
}