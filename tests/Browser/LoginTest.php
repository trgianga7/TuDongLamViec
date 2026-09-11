<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    public function test_login_once(): void
    {
        $this->browse(function (Browser $browser) {

            $browser->visit('https://hdnd.thainguyen.gov.vn/login');

            dump('Bạn có 30 giây để đăng nhập và nhập CAPTCHA...');

            $browser->pause(30000);

            $browser->assertPathIsNot('/login');

            dump('Session đã được lưu.');
        });
    }

    /*public function test_login_quyDauTu(): void
    {
        $this->browse(function (Browser $browser) {

            $browser->visit('https://hnfunds.vn/login');

            dump('Bạn có 60 giây để đăng nhập và nhập CAPTCHA...');

            $browser->pause(60000);

            $browser->assertPathIsNot('/login');

            dump('Session đã được lưu.');
        });
    }*/
}