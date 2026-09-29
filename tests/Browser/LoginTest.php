<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Support\Facades\File;

class LoginTest extends DuskTestCase
{
    public function test_login_once(): void
    {
        $this->browse(function (Browser $browser) {

            $browser->visit('https://hdnd.thainguyen.gov.vn/login');

            dump('Bạn có 30 giây để đăng nhập và nhập CAPTCHA...');

            $browser->pause(30000);

            $browser->assertPathIsNot('/login');

            $ten = trim($browser->script("
                const el = document.querySelector('.user-menu > a .hidden-xs');
                return el ? el.textContent.trim() : '';
            ")[0] ?? '');

            $info = $browser->script("
                const p = document.querySelector('.user-header p');
                if (!p) return ['', ''];

                const text = p.innerText.split('\\n').map(v => v.trim()).filter(Boolean);

                const role = text[1] || '';
                const date = text[2] || '';

                return [role, date];
            ")[0] ?? ['', ''];

            $vaiTro = $info[0] ?? '';
            $ngay = $info[1] ?? '';

            File::put(
                base_path('tests/Browser/session.json'),
                json_encode([
                    'logged_in' => true,
                    'time' => now()->format('d/m/Y H:i:s'),
                    'name' => $ten,
                    'role' => $vaiTro,
                    'login_date' => $ngay
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );

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