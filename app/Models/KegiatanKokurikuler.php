<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Loggable;

class KegiatanKokurikuler extends Model
{
    use HasFactory, SoftDeletes, Loggable;

    /**
     * Nama tabel yang terhubung dengan model ini.
     *
     * @var string
     */
    protected $table = 'kegiatan_kokurikuler';

    /**
     * Atribut yang diizinkan untuk diisi secara massal (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tema_kokurikuler_id',
        'no_urut',
        'nama_kegiatan_kokurikuler',
        'tujuan_akhir_kegiatan',
        'profil_lulusan_id',
        'tingkat',
    ];

    /**
     * Relasi ke tabel Tema Kokurikuler.
     * (Setiap Kegiatan Kokurikuler memiliki 1 Tema)
     */
    public function temaKokurikuler()
    {
        return $this->belongsTo(TemaKokurikuler::class, 'tema_kokurikuler_id');
    }

    /**
     * Relasi ke tabel Profil Lulusan.
     * (Setiap Kegiatan Kokurikuler memiliki 1 Profil Lulusan yang dituju)
     */
    public function profilLulusan()
    {
        return $this->belongsTo(ProfilLulusan::class, 'profil_lulusan_id');
    }
}