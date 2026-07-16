<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Loggable;

class Nilai extends Model
{
    use HasFactory, SoftDeletes, Loggable;

    /**
     * Nama tabel yang terhubung dengan model ini.
     *
     * @var string
     */
    protected $table = 'nilai';

    /**
     * Atribut yang diizinkan untuk diisi secara massal (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'kelas_id',
        'siswa_id',
        'mata_pelajaran_id',
        'nilai_sumatif',
        'rata_sumatif',
        'psts',
        'psas',
        'nilai_rapor',
    ];

    /**
     * Konversi tipe data atribut (Casting).
     * Sangat penting agar JSON otomatis menjadi array di PHP, 
     * dan angka desimal tetap akurat (2 angka di belakang koma).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'nilai_sumatif' => 'array',
        'psts'          => 'decimal:2',
        'psas'          => 'decimal:2',
    ];

    /**
     * Relasi ke tabel Kelas.
     * (Nilai ini tercatat di Kelas apa)
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Relasi ke tabel Siswa.
     * (Milik siapa nilai ini)
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    /**
     * Relasi ke tabel Mata Pelajaran.
     * (Nilai untuk Mata Pelajaran apa)
     */
    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }
}