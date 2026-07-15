<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Loggable;

class NilaiKorikuler extends Model
{
    use HasFactory, SoftDeletes, Loggable;

    /**
     * Nama tabel yang terhubung dengan model ini.
     *
     * @var string
     */
    protected $table = 'nilai_korikuler'; // Jika Anda perbaiki typo di migrasi, jangan lupa ubah ini juga ya!

    /**
     * Atribut yang diizinkan untuk diisi secara massal (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'siswa_id',
        'kegiatan_kokurikuler_id',
        'profil_lulusan_id',
        'kelas_id',
        'predikat',
    ];

    /**
     * Relasi ke tabel Siswa.
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    /**
     * Relasi ke tabel Kegiatan Kokurikuler.
     */
    public function kegiatanKokurikuler()
    {
        return $this->belongsTo(KegiatanKokurikuler::class, 'kegiatan_kokurikuler_id');
    }

    /**
     * Relasi ke tabel Profil Lulusan.
     */
    public function profilLulusan()
    {
        return $this->belongsTo(ProfilLulusan::class, 'profil_lulusan_id');
    }

    /**
     * Relasi ke tabel Kelas.
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
}