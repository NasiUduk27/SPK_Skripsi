<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kriteria',
        'tipe', // 'benefit' atau 'cost'
        'bobot', // nilai 0-1
    ];

    // Relasi balik ke NilaiAlternatif
    public function nilaiAlternatifs()
    {
        return $this->hasMany(NilaiAlternatif::class);
    }
}
