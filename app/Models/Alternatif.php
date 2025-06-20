<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alternatif extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_alternatif', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function nilaiAlternatifs()
    {
        return $this->hasMany(NilaiAlternatif::class);
    }
    public function getNilaiByKriteria($kriteria)
    {
        return $this->nilaiAlternatifs()->where('kriteria_id', $kriteria->id)->first();
    }
}
