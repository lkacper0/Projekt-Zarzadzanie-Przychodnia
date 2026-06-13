<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('glowna');
});

Route::get('/o-nas', function () {
    return view('about');
});
