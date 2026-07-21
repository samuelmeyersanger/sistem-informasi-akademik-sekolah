<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Loggable;

class Kehadiran extends Model
{
    use HasFactory, SoftDeletes, Loggable;

    /**
     * Nama tabel yang terhubung dengan model ini.
     *
     * @var string
     */
    protected $table = 'kehadiran';

    /**
     * Atribut yang diizinkan untuk diisi secara massal (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'siswa_id',
        'kelas_id',
        'semester_id',
        'sakit',
        'izin',
        'tanpa_keterangan',
    ];

    /**
     * Relasi ke tabel Siswa.
     * (Data kehadiran ini milik siapa)
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    /**
     * Relasi ke tabel Kelas.
     * (Data kehadiran ini dicatat di kelas apa)
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Relasi ke tabel Semester.
     * (Kehadiran ini berada di semester apa)
     */
    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }
}