<?php

namespace Tests\Browser\Actions;

use Laravel\Dusk\Browser;
use Tests\Browser\Data\KetQuaGiamSatData;
use Tests\Browser\Support\DuskLogger;

class ThemKetQuaGiamSat
{
    public static function run(Browser $browser): void
    {
        DuskLogger::start('THÊM KẾT QUẢ GIÁM SÁT');

        $items = KetQuaGiamSatData::danhSach();
        $tongBanGhi = 0;

        DuskLogger::info('Tổng số dữ liệu: '.count($items));

        try {

            foreach ($items as $item) {

                $tongBanGhi++;

                DuskLogger::info('Chương trình: '.$item['noi_dung_chuong_trinh']);
                DuskLogger::info('Kiến nghị: '.$item['kien_nghi']);
                DuskLogger::info('Ngày: '.$item['ngay_nhap']);
                DuskLogger::info('Trạng thái: '.$item['trang_thai']);

                // 1. Mở danh sách chương trình
                $browser->visit('https://hdnd.thainguyen.gov.vn/giam-sat-nghi-quyet-hdnd')
                    ->pause(1000);

                DuskLogger::info('Đã mở danh sách chương trình');

                // 2. Tìm chương trình
                $rows = $browser->elements('table tbody tr');
                DuskLogger::info('Số dòng chương trình: '.count($rows));

                $found = false;

                foreach ($rows as $index => $row) {

                    if (str_contains($row->getText(), $item['noi_dung_chuong_trinh'])) {

                        DuskLogger::info('Tìm thấy chương trình tại dòng '.($index + 1));

                        $selector = 'table tbody tr:nth-child('.($index + 1).') a.action-result';

                        if (! $browser->element($selector)) {
                            throw new \Exception('Không có nút action-result tại dòng '.($index + 1));
                        }

                        $browser->click($selector);
                        $found = true;
                        break;
                    }
                }

                if (! $found) {
                    throw new \Exception('Không tìm thấy chương trình: '.$item['noi_dung_chuong_trinh']);
                }

                // 3. Đợi trang kiến nghị
                $browser->pause(1000);
                DuskLogger::info('Đã vào danh sách kiến nghị');

                // 4. Tìm kiến nghị
                $rows = $browser->elements('table tbody tr');
                DuskLogger::info('Số dòng kiến nghị: '.count($rows));

                $found = false;

                foreach ($rows as $index => $row) {

                    if (str_contains($row->getText(), $item['kien_nghi'])) {

                        DuskLogger::info('Tìm thấy kiến nghị tại dòng '.($index + 1));

                        $selector = 'table tbody tr:nth-child('.($index + 1).') .btnAddProgress';

                        if (! $browser->element($selector)) {
                            throw new \Exception('Không có nút thêm tiến độ tại dòng '.($index + 1));
                        }

                        $browser->click($selector);
                        $found = true;
                        break;
                    }
                }

                if (! $found) {
                    throw new \Exception('Không tìm thấy kiến nghị: '.$item['kien_nghi']);
                }

                DuskLogger::info('Đã mở modal thêm tiến độ');

                // 5. Đợi modal
                $browser->waitFor('#progressNoiDung')
                    ->pause(500);

                // 6. Kiểm tra input ngày
                $dateInfo = $browser->script("
                    const input = document.querySelector('#progressNgayNhap');
                    return {
                        exists: !!input,
                        type: input?.type ?? null
                    };
                ")[0];

                if (! $dateInfo['exists']) {
                    throw new \Exception('Không tìm thấy #progressNgayNhap');
                }

                if ($dateInfo['type'] !== 'date') {
                    throw new \Exception('Input ngày không phải type=date');
                }

                // 7. Set ngày
                $ngay = json_encode($item['ngay_nhap'], JSON_UNESCAPED_UNICODE);

                $browser->script("
                    const input = document.querySelector('#progressNgayNhap');
                    input.value = {$ngay};
                    input.dispatchEvent(new Event('input',{bubbles:true}));
                    input.dispatchEvent(new Event('change',{bubbles:true}));
                ");

                $dateAfter = $browser->script("
                    return document.querySelector('#progressNgayNhap').value;
                ")[0];

                if ($dateAfter !== $item['ngay_nhap']) {
                    throw new \Exception('Không set được ngày: '.$dateAfter);
                }

                DuskLogger::info('Ngày nhập: '.$dateAfter);

                // 8. Trạng thái
                $browser->select('trang_thai', $item['trang_thai']);

                $status = $browser->script("
                    return document.querySelector('#progressTrangThai').value;
                ")[0];

                if ($status != $item['trang_thai']) {
                    throw new \Exception('Không chọn được trạng thái');
                }

                DuskLogger::info('Đã chọn trạng thái');

                // 9. Nội dung
                $noiDung = json_encode($item['noi_dung'], JSON_UNESCAPED_UNICODE);

                $browser->script("
                    const t = document.querySelector('#progressNoiDung');
                    t.value = {$noiDung};
                    t.dispatchEvent(new Event('input',{bubbles:true}));
                    t.dispatchEvent(new Event('change',{bubbles:true}));
                ");

                $content = $browser->script("
                    return document.querySelector('#progressNoiDung').value;
                ")[0];

                if ($content !== $item['noi_dung']) {
                    throw new \Exception('Không nhập được nội dung');
                }

                DuskLogger::info('Đã nhập nội dung');

                // 10. Lưu
                $browser->press('Lưu tiến độ')
                    ->pause(1500);

                DuskLogger::info('Hoàn thành: '.$item['kien_nghi']);
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