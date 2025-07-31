<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlternatifController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\VikorController;

Route::get('/', function () {
    $kriteriaCount = \App\Models\Kriteria::count();
    $alternatifCount = \App\Models\Alternatif::count();
    return view('dashboard', [
        'kriteriaCount' => $kriteriaCount,
        'alternatifCount' => $alternatifCount,
    ]);
})->name('home');

Route::get('/perhitungan', [VikorController::class, 'pilihKriteria'])->name('vikor.pilih');
Route::post('/perhitungan/hitung', [VikorController::class, 'hitung'])->name('vikor.hitung');

Route::resource('kriteria', KriteriaController::class)->except(['show']);

Route::resource('alternatif', AlternatifController::class);
Route::get('alternatif/{alternatif}/input-nilai', [AlternatifController::class, 'inputNilai'])->name('alternatif.inputNilai');
Route::post('alternatif/{alternatif}/simpan-nilai', [AlternatifController::class, 'simpanNilai'])->name('alternatif.simpanNilai');
