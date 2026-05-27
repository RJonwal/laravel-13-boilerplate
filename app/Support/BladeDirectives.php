<?php

namespace App\Support;

use Illuminate\Support\Facades\Blade;

class BladeDirectives
{
    public static function register()
    {
        static::btnLoader();
    }

    protected static function btnLoader()
    {
        Blade::directive('btnLoader', function () {
            return '<img src="'.asset('default/spinner.png').'" alt="spinner-loader" class="fa-spin btn_loader d-none" />';
        });
    }
}