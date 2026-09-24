<?php

use Illuminate\Support\Facades\Route;

// Halaman Utama langsung menampilkan Peta & GPS Dashboard
Route::get('/', function () {
    return view('map');
});

Route::get('/map', function () {
    return view('map');
});
