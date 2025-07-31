<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alternatif extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_alternatif',
    ];

    public function nilaiAlternatifs()
    {
        return $this->hasMany(NilaiAlternatif::class);
    }
    public function getNilaiByKriteria($kriteria)
    {
        return $this->nilaiAlternatifs()->where('kriteria_id', $kriteria->id)->first();
    }
}
