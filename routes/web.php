<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AlternatifController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\VikorController;
use App\Http\Controllers\UserController; // <--- TAMBAHKAN INI
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Routes yang memerlukan otentikasi
Route::middleware(['auth'])->group(function () {
    // Resource route untuk Kriteria
    Route::resource('kriteria', KriteriaController::class);

    // Resource route untuk Alternatif
    Route::resource('alternatif', AlternatifController::class);

    // Custom routes untuk input nilai alternatif
    Route::get('alternatif/{alternatif}/input-nilai', [AlternatifController::class, 'inputNilai'])->name('alternatif.inputNilai');
    Route::post('alternatif/{alternatif}/simpan-nilai', [AlternatifController::class, 'simpanNilai'])->name('alternatif.simpanNilai');

    // Route untuk perhitungan VIKOR
    Route::get('/vikor/hitung', [VikorController::class, 'hitung'])->name('vikor.hitung');

    // <--- TAMBAHKAN RESOURCE ROUTE UNTUK USER INI ---
    Route::resource('users', UserController::class);
});
