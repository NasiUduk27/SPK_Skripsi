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

    public function getKategoriNilaiAttribute()
    {
        $nilai = $this->attributes['nilai'];

        if ($nilai == 2) {
            return 'Rendah (1-4)';
        } elseif ($nilai == 6) {
            return 'Sedang (5-7)';
        } elseif ($nilai == 9) {
            return 'Tinggi (8-10)';
        } else {
            return 'Tidak Valid';
        }
    }

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
