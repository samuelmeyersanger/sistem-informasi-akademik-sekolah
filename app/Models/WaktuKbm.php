<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\JadwalPelajaran;
use App\Traits\Loggable;

class WaktuKbm extends Model
{
    use SoftDeletes, Loggable;

    /**
     * Nama tabel yang terikat dengan model ini.
     *
     * @var string
     */
    protected $table = 'waktu_kbm';

    /**
     * Kolom-kolom yang diizinkan untuk diisi secara massal (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'hari',
        'jam_ke',
        'waktu_mulai',
        'waktu_selesai',
        'kegiatan',
    ];

    /**
     * Casting tipe data otomatis dari Laravel.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'jam_ke' => 'integer',
        'deleted_at' => 'datetime',
    ];

    /* -------------------------------------------------------------------------- */
    /* RELASI ELOQUENT                                                            */
    /* -------------------------------------------------------------------------- */

    /**
     * Relasi ke model JadwalPelajaran.
     * Setiap konfigurasi slot waktu KBM dapat digunakan oleh banyak data jadwal pelajaran (One to Many).
     */
    public function jadwalPelajaran()
    {
        return $this->hasMany(JadwalPelajaran::class, 'waktu_kbm_id');
    }
}