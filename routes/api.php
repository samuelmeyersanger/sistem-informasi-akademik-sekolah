<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AntrianIntegrationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('/layanan-terpadu/kelas', [AntrianIntegrationController::class, 'getKelas']);
Route::get('/layanan-terpadu/cek-pengguna', [AntrianIntegrationController::class, 'cekPengguna']);
Route::post('/layanan-terpadu/auth', [AntrianIntegrationController::class, 'authenticate']);
