<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kriteria',
        'tipe', 
        'bobot',
    ];

    // Relasi balik ke NilaiAlternatif
    public function nilaiAlternatifs()
    {
        return $this->hasMany(NilaiAlternatif::class);
    }
}
