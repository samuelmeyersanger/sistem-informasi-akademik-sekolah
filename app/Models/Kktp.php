<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Loggable;

class Kktp extends Model
{
    use HasFactory, SoftDeletes, Loggable;

    /**
     * Nama tabel yang terhubung dengan model ini.
     *
     * @var string
     */
    protected $table = 'kktp';

    /**
     * Atribut yang diizinkan untuk diisi secara massal (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'kelas_id',
        'siswa_id',
        'tujuan_pembelajaran_id',
        'tercapai',
        'tidak_tercapai',
    ];

    /**
     * Relasi ke tabel Kelas.
     * (Satu nilai KKTP berada di dalam 1 Kelas)
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Relasi ke tabel Siswa.
     * (Satu nilai KKTP dimiliki oleh 1 Siswa)
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    /**
     * Relasi ke tabel Tujuan Pembelajaran.
     * (Satu nilai KKTP merujuk pada 1 Tujuan Pembelajaran)
     */
    public function tujuanPembelajaran()
    {
        return $this->belongsTo(TujuanPembelajaran::class, 'tujuan_pembelajaran_id');
    }
}