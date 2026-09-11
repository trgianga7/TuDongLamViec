<?php

namespace Tests\Browser\Actions;

use DateTime;
use Facebook\WebDriver\WebDriverBy;
use Laravel\Dusk\Browser;
use Tests\Browser\Data\TaiLieuBieuQuyetData;
use Tests\Browser\Support\DuskLogger;

class ThemTaiLieuBieuQuyet
{
    public static function run(Browser $browser): void
    {
        DuskLogger::start('THÊM TÀI LIỆU BIỂU QUYẾT');

        $currentMeeting = null;
        $tongBanGhi = 0;

        try {

            foreach (TaiLieuBieuQuyetData::danhSach() as $item) {

                $tongBanGhi++;

                DuskLogger::info('Cuộc họp: '.$item['ten_cuoc_hop']);
                DuskLogger::info('Tiêu đề: '.$item['tieu_de']);

                // 1. Chuẩn hóa ngày
                $ngayGoc = trim($item['ngay_ban_hanh']);

                if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $ngayGoc)) {

                    [$a, $b, $y] = array_map('intval', explode('/', $ngayGoc));

                    if ($b > 12) {
                        $ngay = sprintf('%02d/%02d/%04d', $b, $a, $y);
                    } else {
                        $ngay = sprintf('%02d/%02d/%04d', $a, $b, $y);
                    }

                } else {

                    $ngay = DateTime::createFromFormat(
                        'Y-m-d',
                        $ngayGoc
                    )->format('d/m/Y');
                }

                // 2. Chỉ mở lại khi đổi cuộc họp
                if ($currentMeeting !== $item['ten_cuoc_hop']) {

                    $currentMeeting = $item['ten_cuoc_hop'];

                    $browser->visit('https://hdnd.thainguyen.gov.vn/lich-hop-da-tao')
                        ->waitFor('.meeting-item')
                        ->pause(500);

                    $items = $browser->elements('.meeting-item');
                    $found = false;

                    foreach ($items as $meeting) {

                        $title = trim(
                            $meeting->findElement(
                                WebDriverBy::cssSelector('.meeting-title')
                            )->getText()
                        );

                        if ($title === $currentMeeting) {

                            DuskLogger::info('Tìm thấy cuộc họp: '.$title);

                            $meeting->findElement(
                                WebDriverBy::cssSelector('.meeting-title a')
                            )->click();

                            $found = true;
                            break;
                        }
                    }

                    if (! $found) {
                        throw new \Exception(
                            'Không tìm thấy cuộc họp: '.$currentMeeting
                        );
                    }

                    $browser->pause(800)
                        ->click('a[href="#xu-ly-van-ban-den"]')
                        ->pause(300)
                        ->click('#xu-ly-van-ban-den a.btn.btn-danger.btn-sm')
                        ->pause(800);

                    DuskLogger::info('Đã mở giao diện tài liệu');
                }

                // 3. Tạo form tài liệu
                $browser->click('button[onclick="addDoc()"]')
                    ->waitFor('.doc-item:last-child')
                    ->pause(300);

                DuskLogger::info('Đã tạo form tài liệu');

                $soKyHieu = json_encode($item['so_ky_hieu'], JSON_UNESCAPED_UNICODE);
                $tieuDe   = json_encode($item['tieu_de'], JSON_UNESCAPED_UNICODE);
                $moTa     = json_encode($item['mo_ta'], JSON_UNESCAPED_UNICODE);
                $ngayJs   = json_encode($ngay, JSON_UNESCAPED_UNICODE);

                // 4. Nhập dữ liệu
                $browser->script("
                    const doc = document.querySelector('#documents .doc-item:last-child');

                    const loai = doc.querySelector('select.loai');
                    loai.value = 'vote';
                    loai.dispatchEvent(new Event('change', { bubbles: true }));

                    doc.querySelector('input[name*=\"[so_ky_hieu]\"]').value = {$soKyHieu};
                    doc.querySelector('input[name*=\"[tieu_de]\"]').value = {$tieuDe};
                    doc.querySelector('input[name*=\"[moTa]\"]').value = {$moTa};

                    const dateInput = doc.querySelector('input[name*=\"[ngay_ban_hanh]\"]');
                    dateInput.value = {$ngayJs};

                    [loai, dateInput].forEach(el => {
                        el.dispatchEvent(new Event('input', { bubbles: true }));
                        el.dispatchEvent(new Event('change', { bubbles: true }));
                        el.dispatchEvent(new Event('blur', { bubbles: true }));
                    });
                ");

                $ngayThucTe = $browser->script("
                    return document.querySelector(
                        '#documents .doc-item:last-child input[name*=\"[ngay_ban_hanh]\"]'
                    ).value;
                ");

                DuskLogger::info('Ngày ban hành: '.($ngayThucTe[0] ?? ''));

                // 5. Upload file
                if (! empty($item['file'])) {

                    $filePath = base_path('tests/Browser/File/'.$item['file']);

                    if (! file_exists($filePath)) {
                        throw new \Exception('Không tìm thấy file: '.$filePath);
                    }

                    $browser->script("
                        const inputs = document.querySelectorAll('#documents .doc-item input[type=file]');
                        inputs.forEach(i => i.removeAttribute('id'));
                        inputs[inputs.length - 1].id = 'current-file-upload';
                    ");

                    $browser->attach('#current-file-upload', $filePath);

                    DuskLogger::info('Đã upload file: '.$item['file']);

                } else {

                    DuskLogger::info('Không có file đính kèm');
                }

                // 6. Lưu
                $browser->press('Lưu lại')
                    ->pause(1200);

                DuskLogger::info('Hoàn thành: '.$item['tieu_de']);
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