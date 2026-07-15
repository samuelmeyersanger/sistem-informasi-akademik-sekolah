<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GayaBelajarHasil extends Model
{
    use HasFactory;

    protected $table = 'gaya_belajar_hasil';

    protected $fillable = [
        'siswa_id',
        'skor_visual',
        'skor_auditory',
        'skor_kinesthetic',
        'gaya_dominan',
    ];

    /**
     * Relasi balik ke tabel Siswa
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}