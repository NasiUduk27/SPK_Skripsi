<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Kriteria;

class KriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Mengosongkan tabel kriteria sebelum diisi ulang
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('kriterias')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $kriterias = [
            // 8 Kriteria Tipe Benefit
            ['nama_kriteria' => 'Potensi Keuntungan', 'tipe' => 'benefit', 'bobot' => 30],
            ['nama_kriteria' => 'Tingkat Permintaan Pasar', 'tipe' => 'benefit', 'bobot' => 30],
            ['nama_kriteria' => 'Keberlanjutan Usaha', 'tipe' => 'benefit', 'bobot' => 20],
            ['nama_kriteria' => 'Kemudahan Pemasaran', 'tipe' => 'benefit', 'bobot' => 20],
            ['nama_kriteria' => 'Ketersediaan Bahan Baku', 'tipe' => 'benefit', 'bobot' => 15],
            ['nama_kriteria' => 'Fleksibilitas Waktu', 'tipe' => 'benefit', 'bobot' => 10],
            ['nama_kriteria' => 'Inovasi dan Kreativitas', 'tipe' => 'benefit', 'bobot' => 15],
            ['nama_kriteria' => 'Skalabilitas Usaha', 'tipe' => 'benefit', 'bobot' => 25],
            // 2 Kriteria Tipe Cost
            ['nama_kriteria' => 'Modal Awal', 'tipe' => 'cost', 'bobot' => 25],
            ['nama_kriteria' => 'Tingkat Persaingan', 'tipe' => 'cost', 'bobot' => 15],
        ];

        // Memasukkan data ke dalam tabel
        foreach ($kriterias as $kriteria) {
            Kriteria::create($kriteria);
        }
    }
}
