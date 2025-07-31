<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlternatifController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\VikorController;

Route::get('/', function () {
    $kriteriaCount = \App\Models\Kriteria::count();
    $alternatifCount = \App\Models\Alternatif::count();
    return view('dashboard', compact('kriteriaCount', 'alternatifCount'));
})->name('home');

Route::get('/kriteria', [KriteriaController::class, 'index'])->name('kriteria.index');
Route::post('/kriteria/proses', [KriteriaController::class, 'prosesPemilihan'])->name('kriteria.prosesPemilihan');

Route::resource('alternatif', AlternatifController::class)->except(['show', 'inputNilai', 'simpanNilai']);
Route::post('/alternatif/simpan-dan-lanjutkan', [AlternatifController::class, 'simpanDanLanjutkan'])->name('alternatif.simpanDanLanjutkan');

Route::get('/perhitungan', [VikorController::class, 'pilihKriteria'])->name('vikor.pilih');
Route::post('/perhitungan/hitung', [VikorController::class, 'hitung'])->name('vikor.hitung');
