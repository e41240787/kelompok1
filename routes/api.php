<?php

use App\Http\Controllers\GpsDataController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes untuk Backend GPS Tracking
|--------------------------------------------------------------------------
|
| Route dibawah ini digunakan untuk:
| 1. Endpoint penampung data dari Sensor/Hardware (POST /api/gps)
| 2. Endpoint pembaca data untuk Front-End (GET /api/gps & GET /api/gps/latest)
|
*/

// Endpoint untuk mengambil lokasi GPS paling baru (untuk live tracking)
Route::get('/gps/latest', [GpsDataController::class, 'latest']);

// Endpoint CRUD utama data GPS
Route::get('/gps', [GpsDataController::class, 'index']);       // Ambil semua histori data
Route::post('/gps', [GpsDataController::class, 'store']);      // Menerima data dari sensor IoT
Route::get('/gps/{id}', [GpsDataController::class, 'show']);   // Ambil detail data spesifik
Route::delete('/gps/{id}', [GpsDataController::class, 'destroy']); // Hapus log data spesifik
