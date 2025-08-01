<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Kriteria;

class KriteriaSeeder extends Seeder
{
    /**
     * Menjalankan proses seeding untuk database.
     * Latar belakang kriteria ini didasarkan pada analisis kelayakan bisnis umum
     * yang relevan untuk usaha skala kecil hingga menengah.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('kriterias')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $kriterias = [

            [
                'nama_kriteria' => 'Banyak Peminatnya?',
                'tipe' => 'benefit',
                'bobot' => 30,
            ],
            [
                'nama_kriteria' => 'Potensi Untung Besar?',
                'tipe' => 'benefit',
                'bobot' => 25,
            ],
            [
                'nama_kriteria' => 'Bahan Baku Gampang Dicari?',
                'tipe' => 'benefit',
                'bobot' => 20,
            ],
            [
                'nama_kriteria' => 'Mudah Dipasarkan?',
                'tipe' => 'benefit',
                'bobot' => 20,
            ],
            [
                'nama_kriteria' => 'Bisa Cepat Berkembang?',
                'tipe' => 'benefit',
                'bobot' => 15,
            ],
            [
                'nama_kriteria' => 'Bisa Dijalankan Jangka Panjang?',
                'tipe' => 'benefit',
                'bobot' => 15,
            ],
            [
                'nama_kriteria' => 'Punya Keunikan/Inovasi?',
                'tipe' => 'benefit',
                'bobot' => 10,
            ],
            [
                'nama_kriteria' => 'Waktu Kerjanya Fleksibel?',
                'tipe' => 'benefit',
                'bobot' => 5,
            ],

            [
                'nama_kriteria' => 'Butuh Modal Besar?',
                'tipe' => 'cost',
                'bobot' => 25,
            ],
            [
                'nama_kriteria' => 'Banyak Saingannya?',
                'tipe' => 'cost',
                'bobot' => 20,
            ],
        ];

        foreach ($kriterias as $kriteria) {
            Kriteria::create($kriteria);
        }
    }
}
