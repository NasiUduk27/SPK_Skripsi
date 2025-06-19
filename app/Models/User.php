<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin', // Sudah ada dan benar
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean', // Sudah ada dan benar

    ];

    // Relasi ke Kriteria (Jika kriteria juga spesifik per user, yang jarang)
    // Jika kriteria global, relasi ini tidak perlu, atau hanya admin yang punya relasi ini
    public function kriterias()
    {
        return $this->hasMany(Kriteria::class);
    }

    // Relasi ke Alternatif
    public function alternatifs()
    {
        return $this->hasMany(Alternatif::class);
    }

    // Relasi ke NilaiAlternatif
    public function nilaiAlternatifs()
    {
        return $this->hasMany(NilaiAlternatif::class);
    }

    /**
     * Helper method to check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return (bool) $this->is_admin; // Mengembalikan true jika is_admin adalah 1/true
    }
}
