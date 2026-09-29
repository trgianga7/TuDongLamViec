<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DuskController;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/dusk', 'dusk.index');