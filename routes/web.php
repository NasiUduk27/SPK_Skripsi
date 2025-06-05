<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\AlternatifController;
use App\Http\Controllers\VikorController; // Nanti kita buat ini
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Routes untuk Kriteria (butuh login)
Route::resource('kriteria', KriteriaController::class)->middleware('auth');

// Routes untuk Alternatif (butuh login)
Route::resource('alternatif', AlternatifController::class)->middleware('auth');
Route::get('alternatif/{alternatif}/input-nilai', [AlternatifController::class, 'inputNilai'])->name('alternatif.inputNilai')->middleware('auth');
Route::post('alternatif/{alternatif}/simpan-nilai', [AlternatifController::class, 'simpanNilai'])->name('alternatif.simpanNilai')->middleware('auth');

// Route untuk Perhitungan VIKOR (butuh login)
Route::get('/vikor/hitung', [VikorController::class, 'hitung'])->name('vikor.hitung')->middleware('auth');