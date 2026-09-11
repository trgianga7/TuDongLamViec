<?php

namespace Tests\Browser\Actions;

use Laravel\Dusk\Browser;
use Tests\Browser\Data\NoiDungGiamSatData;
use Tests\Browser\Support\DuskLogger;
use Tests\Browser\Support\DuskNotify;

class ThemNoiDungGiamSat
{
    public static function run(Browser $browser): void
    {
        DuskLogger::start('THÊM NỘI DUNG GIÁM SÁT');

        $tongBanGhi = 0;

        try {

            foreach (NoiDungGiamSatData::danhSach() as $item) {

                $tongBanGhi++;

                DuskLogger::info('Chương trình: '.$item['noi_dung_chuong_trinh']);
                DuskLogger::info('Kiến nghị: '.$item['noi_dung']);

                // 1. Vào danh sách chương trình
                $browser->visit('https://hdnd.thainguyen.gov.vn/giam-sat-nghi-quyet-hdnd')
                    ->pause(1000);

                DuskLogger::info('Đã mở danh sách chương trình');

                // 2. Tìm đúng chương trình
                $rows = $browser->elements('table tbody tr');
                $found = false;

                foreach ($rows as $index => $row) {

                    if (str_contains($row->getText(), $item['noi_dung_chuong_trinh'])) {

                        $browser->click(
                            'table tbody tr:nth-child('.($index + 1).') a.action-result'
                        );

                        DuskLogger::info('Đã chọn chương trình');

                        $found = true;
                        break;
                    }
                }

                if (! $found) {
                    throw new \Exception(
                        'Không tìm thấy chương trình: '.$item['noi_dung_chuong_trinh']
                    );
                }

                // 3. Mở modal
                $browser->pause(1000)
                    ->click('#btnAddResult')
                    ->pause(500);

                DuskLogger::info('Đã mở modal');

                // 4. Nhập kiến nghị
                $browser->type('noi_dung', $item['noi_dung']);

                DuskLogger::info('Đã nhập nội dung kiến nghị');

                // 5. Chọn nhiều đơn vị
                $ids = json_encode($item['doi_tuong_ids']);

                $browser->script("
                    const values = $ids;
                    const select = $('#resultDoiTuong');
                    select.val(values).trigger('change');
                ");

                DuskLogger::info(
                    'Đã chọn '.count($item['doi_tuong_ids']).' đơn vị'
                );

                // 6. Upload tài liệu (nếu có)
                if (!empty($item['files'])) {

                    foreach ($item['files'] as $i => $file) {
                
                        $path = base_path('tests/Browser/File/'.$file);
                
                        if (!file_exists($path)) {
                            throw new \Exception("Không tìm thấy file: {$file}");
                        }
                
                        $browser->click('#btnAddResultAttachment')
                                ->pause(300);
                
                        $browser->script("
                            const rows = document.querySelectorAll('#resultAttachmentContainer .attachment-row');
                            const row = rows[rows.length - 1];
                
                            row.querySelector('input[name$=\"[ten]\"]').id = 'upload-name';
                            row.querySelector('input[type=file]').id = 'upload-file';
                        ");
                
                        $browser->type('#upload-name', $item['ten_tai_lieu'][$i] ?? '')
                                ->attach('#upload-file', $path)
                                ->pause(300);
                
                        DuskLogger::info(
                            "Đã upload: ".($item['ten_tai_lieu'][$i] ?? '')." -> {$file}"
                        );
                    }
                }

                // 7. Lưu
                $browser->press('Lưu kết quả');
                
                DuskNotify::verify($browser);

                DuskLogger::info('Hoàn thành: '.$item['noi_dung']);
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