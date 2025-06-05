<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alternatif extends Model
{
    use HasFactory;

    protected $fillable = ['nama_alternatif'];

    public function nilaiAlternatifs()
    {
        return $this->hasMany(NilaiAlternatif::class);
    }

    // Helper untuk mendapatkan nilai kriteria tertentu
    public function getNilaiByKriteria(Kriteria $kriteria)
    {
        return $this->nilaiAlternatifs()->where('kriteria_id', $kriteria->id)->first();
    }
}