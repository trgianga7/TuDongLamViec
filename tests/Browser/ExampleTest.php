<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Tests\Browser\Actions\ThemNguoiDung;
use Tests\Browser\Actions\ThemKhoaHop;
use Tests\Browser\Actions\ThemKyHop;
use Tests\Browser\Actions\ThemDanhMucFile;
use Tests\Browser\Actions\ThemDonVi;
use Tests\Browser\Actions\ThemPhongHop;
use Tests\Browser\Actions\ThemGiamSat;
use Tests\Browser\Actions\ThemNoiDungGiamSat;
use Tests\Browser\Actions\ThemKetQuaGiamSat;
use Tests\Browser\Actions\ThemTaiLieuBieuQuyet;
use Tests\Browser\Actions\ThemPhieuLayYKien;
use Tests\Browser\Actions\ThemCuocHopNoiBo;
use Tests\Browser\Actions\ThemDoiTuongGiamSat;
use Tests\Browser\Actions\ThemChuThe;
use Tests\Browser\Actions\TuDongDiemDanh;
use Tests\Browser\Actions\QuyDauTuPT\ThemDuAnDangThucHien;
use Tests\Browser\Actions\ThemCuocHop;
use Tests\Browser\Actions\ThemDanhMucKienNghi_KhoaHop;
use Tests\Browser\Actions\ThemDanhMucKienNghi_KyHop;
use Tests\Browser\Actions\ThemDanhMucKienNghi_LinhVuc;
use Tests\Browser\Actions\ThemNghiQuyetBanHanh;
use Tests\Browser\Data\UserData;

class ExampleTest extends DuskTestCase
{
    public function test_auto(): void
    {
        $this->browse(function (Browser $browser) {

            //ThemNguoiDung::run($browser);

            //ThemKhoaHop::run($browser);

            //ThemKyHop::run($browser);

            //ThemDanhMucFile::run($browser);

            //ThemDonVi::run($browser);

            //ThemPhongHop::run($browser);

            //ThemGiamSat::run($browser);

            //ThemNoiDungGiamSat::run($browser);

            //ThemKetQuaGiamSat::run($browser);

            //ThemTaiLieuBieuQuyet::run($browser);

            //ThemPhieuLayYKien::run($browser);

            //ThemCuocHopNoiBo::run($browser);

            //ThemDoiTuongGiamSat::run($browser);

            //ThemChuThe::run($browser);

            //TuDongDiemDanh::run($browser);

            //ThemCuocHop::run($browser);

            //ThemDanhMucKienNghi_KhoaHop::run($browser);

            //ThemDanhMucKienNghi_KyHop::run($browser);

            //ThemDanhMucKienNghi_LinhVuc::run($browser);

            ThemNghiQuyetBanHanh::run($browser);



            //=======Quy dau tu=======================================//

            //ThemDuAnDangThucHien::run($browser);

        });
    }

}