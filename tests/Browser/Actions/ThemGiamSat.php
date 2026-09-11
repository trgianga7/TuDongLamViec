<?php

namespace Tests\Browser\Actions;

use Laravel\Dusk\Browser;
use Tests\Browser\Data\GiamSatData;
use Tests\Browser\Support\DuskLogger;
use Tests\Browser\Support\DuskNotify;

class ThemGiamSat
{
    public static function run(Browser $browser): void
    {
        DuskLogger::start('THÊM GIÁM SÁT');

        $tongBanGhi = 0;

        try {

            foreach (GiamSatData::danhSach() as $gs) {

                $tongBanGhi++;

                DuskLogger::info('Nội dung: '.$gs['noi_dung']);
                DuskLogger::info('Loại thông tin: '.$gs['loai_tt']);
                DuskLogger::info('Chủ thể: '.$gs['chu_the']);
                DuskLogger::info('Hình thức: '.$gs['hinh_thuc']);

                $browser->visit('https://hdnd.thainguyen.gov.vn/giam-sat-nghi-quyet-hdnd/create')
                    ->pause(1000)

                    ->select('loai_tt', $gs['loai_tt'])
                    ->select('chu_the', $gs['chu_the'])
                    ->select('hinh_thuc', $gs['hinh_thuc'])

                    ->type('noi_dung', $gs['noi_dung']);
                    //->type('bat_dau', $gs['bat_dau'])
                    //->type('ket_thuc', $gs['ket_thuc']);
                
                $browser->script("
                    const setDate = (name, value) => {
                        const input = document.querySelector(`input[name=\"\${name}\"]`);
                        if (!input) return;
                
                        input.value = value;
                
                        ['input','change','blur'].forEach(evt=>{
                            input.dispatchEvent(new Event(evt,{bubbles:true}));
                        });
                    };
                
                    setDate('bat_dau', '{$gs['bat_dau']}');
                    setDate('ket_thuc', '{$gs['ket_thuc']}');
                ");

                DuskLogger::info('Đã nhập thông tin cơ bản');

                //Quyết định
                if (!empty($gs['quyet_dinh'] ?? [])) {

                    foreach ($gs['quyet_dinh'] as $index => $qd) {

                        if ($index > 0) {
                            $browser->click('.btnAddQuyetDinh')
                                ->pause(300);
                        }

                        $browser->script("
                            const items = document.querySelectorAll('.quyet-dinh-item');
                            items.forEach(i => i.removeAttribute('id'));
                            items[items.length - 1].id = 'current-quyet-dinh';
                        ");

                        $browser->type(
                            '#current-quyet-dinh input[name*="[ten]"]',
                            $qd['ten']
                        );

                        $filePath = base_path('tests/Browser/File/'.$qd['file']);

                        if (!file_exists($filePath)) {
                            throw new \Exception('Không tìm thấy file: '.$filePath);
                        }

                        $browser->attach(
                            '#current-quyet-dinh input[type="file"]',
                            $filePath
                        );

                        DuskLogger::info(
                            'Đã thêm quyết định: '.$qd['ten'].' - '.basename($qd['file'])
                        );
                    }

                } else {

                    DuskLogger::info('Không có quyết định');
                }

                //Tài liệu đính kèm
                if (!empty($gs['tai_lieu'] ?? [])) {

                    foreach ($gs['tai_lieu'] as $index => $tl) {

                        // Bỏ qua nhóm rỗng
                        if (empty($tl['files'])) {
                            DuskLogger::info(
                                'Bỏ qua tài liệu rỗng (Danh mục '.$tl['danh_muc'].')'
                            );
                            continue;
                        }

                        if ($index > 0) {
                            $browser->click('#btnAddTaiLieu')
                                ->pause(300);
                        }

                        $browser->script("
                            const items = document.querySelectorAll('.tai-lieu-item');
                            items.forEach(i => i.removeAttribute('id'));
                            items[items.length - 1].id = 'current-tai-lieu';
                        ");

                        // Chọn danh mục
                        $browser->script("
                            const select = document.querySelector(
                                '#current-tai-lieu select[name*=\"[danh_muc]\"]'
                            );

                            select.value = '{$tl['danh_muc']}';
                            select.dispatchEvent(new Event('change', { bubbles:true }));

                            if (window.jQuery) {
                                $(select).trigger('change');
                            }
                        ");

                        // Gắn ID cho input file
                        $browser->script("
                            const input = document.querySelector(
                                '#current-tai-lieu input[type=file]'
                            );
                            input.id = 'current-tai-lieu-file';
                        ");

                        $paths = [];

                        foreach ($tl['files'] as $file) {

                            $path = base_path('tests/Browser/File/'.$file);

                            if (!file_exists($path)) {
                                throw new \Exception('Không tìm thấy file: '.$path);
                            }

                            $paths[] = $path;
                        }

                        // Nếu không có file thì bỏ qua
                        if (count($paths) === 0) {
                            DuskLogger::info(
                                'Bỏ qua tài liệu không có file (Danh mục '.$tl['danh_muc'].')'
                            );
                            continue;
                        }

                        $browser->pause(100);

                        $browser->element('#current-tai-lieu-file')
                            ->sendKeys(implode("\n", $paths));

                        DuskLogger::info(
                            'Đã thêm tài liệu: Danh mục '.$tl['danh_muc'].
                            ' - '.implode(', ', $tl['files'])
                        );
                    }

                } else {

                    DuskLogger::info('Không có tài liệu đính kèm');
                }

                //Đối tượng trực tiếp
                if (!empty($gs['doi_tuong_truc_tiep'])) {

                    $ids = json_encode(array_map('strval', $gs['doi_tuong_truc_tiep']));

                    $browser->script("
                        const values = {$ids};
                        const select = $('select[name=\"doi_tuong_truc_tiep[]\"]');
                        select.val(values).trigger('change');
                    ");
                }

                DuskLogger::info(
                    'Đã chọn '.count($gs['doi_tuong_truc_tiep']).' đối tượng trực tiếp'
                );

                //Đối tượng gián tiếp
                if (!empty($gs['doi_tuong_gian_tiep'])) {

                    $ids = json_encode(array_map('strval', $gs['doi_tuong_gian_tiep']));

                    $browser->script("
                        const values = {$ids};
                        const select = $('select[name=\"doi_tuong_gian_tiep[]\"]');
                        select.val(values).trigger('change');
                    ");
                }

                DuskLogger::info(
                    'Đã chọn '.count($gs['doi_tuong_gian_tiep']).' đối tượng gián tiếp'
                );

                // lưu
                $browser->press('Lưu lại')
                    ->assertPathIs('/giam-sat-nghi-quyet-hdnd');
                
                DuskNotify::verify($browser);

                DuskLogger::info('Hoàn thành: '.$gs['noi_dung']);
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