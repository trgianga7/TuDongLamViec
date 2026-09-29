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

        $tong = 0;

        try {

            foreach (GiamSatData::danhSach() as $gs) {

                $tong++;

                DuskLogger::info("Nội dung: {$gs['noi_dung']}");
                DuskLogger::info("Loại: {$gs['loai_tt']}");
                DuskLogger::info("Chủ thể: {$gs['chu_the']}");
                DuskLogger::info("Hình thức: {$gs['hinh_thuc']}");

                $browser->visit(
                    'https://hdnd.thainguyen.gov.vn/giam-sat-nghi-quyet-hdnd/create'
                )->pause(1200);

                self::selectByText($browser,'loai_tt',$gs['loai_tt']);
                self::selectByText($browser,'chu_the',$gs['chu_the']);
                self::selectByText($browser,'hinh_thuc',$gs['hinh_thuc']);

                $browser->type('noi_dung',$gs['noi_dung']);

                $browser->script("
                    const setDate=(n,v)=>{
                        const i=document.querySelector(`input[name=\"\${n}\"]`);
                        if(!i) return;
                        i.value=v;
                        ['input','change','blur'].forEach(e=>{
                            i.dispatchEvent(new Event(e,{bubbles:true}));
                        });
                    };
                    setDate('bat_dau','{$gs['bat_dau']}');
                    setDate('ket_thuc','{$gs['ket_thuc']}');
                ");

                DuskLogger::info('Đã nhập thông tin');

                // ===== Quyết định =====
                foreach ($gs['quyet_dinh'] as $i => $qd) {

                    if ($i > 0) {
                        $browser->click('.btnAddQuyetDinh')->pause(300);
                    }

                    $browser->script("
                        const items=document.querySelectorAll('.quyet-dinh-item');
                        items.forEach(x=>x.removeAttribute('id'));
                        items[items.length-1].id='qd';
                    ");

                    $browser->type('#qd input[name*="[ten]"]',$qd['ten']);

                    $browser->attach(
                        '#qd input[type=file]',
                        base_path('tests/Browser/File/'.$qd['file'])
                    );
                }

                // ===== Tài liệu =====
                foreach ($gs['tai_lieu'] as $i => $tl) {

                    if (empty($tl['files'])) continue;

                    if ($i > 0) {
                        $browser->click('#btnAddTaiLieu')->pause(300);
                    }

                    $browser->script("
                        const items=document.querySelectorAll('.tai-lieu-item');
                        items.forEach(x=>x.removeAttribute('id'));
                        items[items.length-1].id='tl';
                    ");

                    self::selectByText(
                        $browser,
                        '[danh_muc]',
                        $tl['danh_muc'],
                        '#tl select'
                    );

                    $browser->script("
                        document.querySelector('#tl input[type=file]')
                        .id='uploadTL';
                    ");

                    $paths=[];

                    foreach($tl['files'] as $f){
                        $paths[]=base_path('tests/Browser/File/'.$f);
                    }

                    $browser->element('#uploadTL')
                        ->sendKeys(implode("\n",$paths));
                }

                // ===== Đối tượng =====
                self::selectMultiByText(
                    $browser,
                    'doi_tuong_truc_tiep',
                    $gs['doi_tuong_truc_tiep']
                );

                self::selectMultiByText(
                    $browser,
                    'doi_tuong_gian_tiep',
                    $gs['doi_tuong_gian_tiep']
                );

                DuskLogger::info(
                    'ĐTTT: '.implode(', ',$gs['doi_tuong_truc_tiep'])
                );

                DuskLogger::info(
                    'ĐTGT: '.implode(', ',$gs['doi_tuong_gian_tiep'])
                );

                $browser->press('Lưu lại');

                DuskNotify::verify($browser);

                $browser->visit(
                    'https://hdnd.thainguyen.gov.vn/giam-sat-nghi-quyet-hdnd/create'
                )->waitFor('textarea[name="noi_dung"]');

                DuskLogger::info("Hoàn thành: {$gs['noi_dung']}");
                DuskLogger::info(str_repeat('-',50));
            }

            DuskLogger::info("ĐÃ HOÀN THÀNH {$tong} BẢN GHI");

        } catch (\Throwable $e){

            DuskLogger::info(str_repeat('=',50));
            DuskLogger::info("Bản ghi lỗi: {$tong}");
            DuskLogger::info($e->getMessage());
            DuskLogger::info(str_repeat('=',50));

            throw $e;
        }
    }

    // =============================

    private static function selectByText(
        Browser $browser,
        string $name,
        string $text,
        ?string $selector=null
    ): void{

        $target=json_encode(trim($text));

        $selector=$selector
            ? json_encode($selector)
            : "'select[name=\"{$name}\"]'";

        $browser->script("
            const target={$target};
            const sel={$selector};

            const select=document.querySelector(sel);

            if(!select)
                throw new Error('Không thấy select');

            const opt=[...select.options].find(
                o=>o.text.trim()===target
            );

            if(!opt)
                throw new Error('Không tìm thấy option: '+target);

            select.value=opt.value;

            if(window.jQuery){
                $(select).val(opt.value).trigger('change');
            }

            select.dispatchEvent(
                new Event('change',{bubbles:true})
            );
        ");

        $browser->pause(250);
    }

    private static function selectMultiByText(
        Browser $browser,
        string $name,
        array $texts
    ): void{

        if(empty($texts)) return;

        $json=json_encode(array_values($texts));

        $browser->script("
            const targets={$json};

            const select=document.querySelector(
                'select[name=\"{$name}[]\"]'
            );

            if(!select)
                throw new Error('Không thấy select {$name}');

            const values=[];

            targets.forEach(t=>{

                const opt=[...select.options].find(
                    o=>o.text.trim()===t.trim()
                );

                if(opt){
                    values.push(opt.value);
                }
            });

            if(window.jQuery){
                $(select).val(values).trigger('change');
            }else{
                [...select.options].forEach(o=>{
                    o.selected=values.includes(o.value);
                });

                select.dispatchEvent(
                    new Event('change',{bubbles:true})
                );
            }
        ");

        $browser->pause(400);
    }
}