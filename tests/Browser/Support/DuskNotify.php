<?php

namespace Tests\Browser\Support;

use Laravel\Dusk\Browser;

class DuskNotify
{
    public static function verify(Browser $browser): void
    {
        $browser->pause(1200);

        $notify = $browser->script(<<<'JS'
            const visible = (el) => {
                if (!el) return false;

                const style = getComputedStyle(el);

                return style.display !== 'none'
                    && style.visibility !== 'hidden'
                    && el.offsetParent !== null;
            };

            const make = (ok, type, msg) => ({
                ok,
                type,
                msg: (msg || '').trim()
            });

            const fl = document.querySelector('.fl-flasher');

            if (visible(fl)) {

                const type = [...fl.classList].find(c =>
                    ['fl-success','fl-warning','fl-error','fl-info'].includes(c)
                ) || 'fl-info';

                const title = fl.querySelector('.fl-title')?.innerText || '';
                const msg   = fl.querySelector('.fl-message')?.innerText || '';

                const text = title ? `${title}: ${msg}` : msg;

                return make(
                    type === 'fl-success',
                    type.replace('fl-',''),
                    text
                );
            }

            const alert = [...document.querySelectorAll('.alert')]
                .find(a => visible(a));

            if (alert) {

                return make(
                    alert.classList.contains('alert-success'),
                    'alert',
                    alert.innerText
                );
            }

            const toast = [...document.querySelectorAll('.toastify, .toast')]
                .find(t => visible(t));

            if (toast) {

                const ok = /success/i.test(toast.className);

                return make(
                    ok,
                    'toast',
                    toast.innerText
                );
            }

            return null;
        JS)[0];

        if ($notify !== null) {

            if ($notify['ok']) {

                DuskLogger::info(
                    "SUCCESS ({$notify['type']}): {$notify['msg']}"
                );

                return;
            }

            DuskLogger::info(
                "FAILED ({$notify['type']}): {$notify['msg']}"
            );

            throw new \Exception($notify['msg']);
        }

        $path = parse_url($browser->driver->getCurrentURL(), PHP_URL_PATH);

        if (!str_contains($path, '/create')) {

            DuskLogger::info("SUCCESS: Redirect -> {$path}");

            return;
        }

        DuskLogger::info('SUCCESS: Không phát hiện notify');
    }
}