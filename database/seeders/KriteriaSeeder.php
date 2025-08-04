<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Kriteria;

class KriteriaSeeder extends Seeder
{
    /**
     * Menjalankan proses seeding untuk database.
     *
     * Latar Belakang:
     * Kriteria dan bobot ini disusun berdasarkan studi umum mengenai faktor penentu keberhasilan
     * usaha kecil dan menengah (UMKM). Bobot tertinggi diberikan pada aspek fundamental
     * seperti potensi keuntungan dan permintaan pasar. Nama kriteria diubah menjadi
     * format pertanyaan agar sesuai dengan skala penilaian (Rendah, Sedang, Tinggi).
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
                'nama_kriteria' => 'Seberapa besar potensi untungnya?',
                'tipe' => 'benefit',
                'bobot' => 0.20,
            ],
            [
                'nama_kriteria' => 'Seberapa banyak peminat di pasar?',
                'tipe' => 'benefit',
                'bobot' => 0.20,
            ],
            [
                'nama_kriteria' => 'Seberapa besar modal awalnya?',
                'tipe' => 'cost',
                'bobot' => 0.15,
            ],
            [
                'nama_kriteria' => 'Seberapa banyak saingannya?',
                'tipe' => 'cost',
                'bobot' => 0.10,
            ],

            [
                'nama_kriteria' => 'Seberapa tahan lama usahanya?',
                'tipe' => 'benefit',
                'bobot' => 0.10,
            ],
            [
                'nama_kriteria' => 'Seberapa mudah pemasarannya?',
                'tipe' => 'benefit',
                'bobot' => 0.07,
            ],
            [
                'nama_kriteria' => 'Seberapa mudah mencari bahan bakunya?',
                'tipe' => 'benefit',
                'bobot' => 0.05,
            ],
            [
                'nama_kriteria' => 'Seberapa besar peluang untuk berkembang?',
                'tipe' => 'benefit',
                'bobot' => 0.05,
            ],

            [
                'nama_kriteria' => 'Seberapa unik produk/jasanya?',
                'tipe' => 'benefit',
                'bobot' => 0.04,
            ],
            [
                'nama_kriteria' => 'Seberapa fleksibel waktu kerjanya?',
                'tipe' => 'benefit',
                'bobot' => 0.04,
            ],
        ];

        foreach ($kriterias as $kriteria) {
            Kriteria::create($kriteria);
        }
    }
}
