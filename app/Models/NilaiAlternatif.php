<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiAlternatif extends Model
{
    use HasFactory;

    protected $table = 'nilai_alternatifs';

    protected $fillable = [
        'alternatif_id',
        'kriteria_id',
        'nilai',
    ];

    public function alternatif()
    {
        return $this->belongsTo(Alternatif::class);
    }

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class);
    }

    /**
     * Accessor untuk mengkonversi nilai numerik standar ke kategori (Rendah, Sedang, Tinggi).
     * Logika ini harus sesuai dengan 'value' yang digunakan di dropdown input.
     */
    public function getKategoriNilaiAttribute()
    {
        $nilai = $this->attributes['nilai'];

        if ($nilai == 2) { // Standar untuk Rendah (range 1-4)
            return 'Rendah (1-4)';
        } elseif ($nilai == 6) { // Standar untuk Sedang (range 5-7)
            return 'Sedang (5-7)';
        } elseif ($nilai == 9) { // Standar untuk Tinggi (range 8-10)
            return 'Tinggi (8-10)';
        } else {
            return 'Tidak Valid'; // Jika ada nilai lain yang tersimpan (seharusnya tidak terjadi)
        }
    }

    /**
     * Accessor untuk mengkonversi nilai numerik standar menjadi rentang angka (jika ingin ditampilkan).
     */
    public function getRangeAngkaAttribute()
    {
        $nilai = $this->attributes['nilai'];

        if ($nilai == 2) {
            return '1-4';
        } elseif ($nilai == 6) {
            return '5-7';
        } elseif ($nilai == 9) {
            return '8-10';
        } else {
            return 'N/A';
        }
    }
}
