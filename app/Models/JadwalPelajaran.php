<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\WaktuKbm;
use App\Models\Kelas;
use App\Models\KodeGuru;
use App\Models\Ruangan;
use App\Traits\Loggable;

class JadwalPelajaran extends Model
{
    use SoftDeletes, Loggable;

    /**
     * Nama tabel yang terikat dengan model ini.
     *
     * @var string
     */
    protected $table = 'jadwal_pelajaran';

    /**
     * Kolom-kolom yang diizinkan untuk diisi secara massal (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'hari',
        'waktu_kbm_id',
        'kelas_id',
        'kode_guru_id',
        'ruangan_id',
    ];

    /**
     * Casting tipe data otomatis dari Laravel.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'waktu_kbm_id' => 'integer',
        'kelas_id' => 'integer',
        'kode_guru_id' => 'integer',
        'ruangan_id' => 'integer',
        'deleted_at' => 'datetime',
    ];

    /* -------------------------------------------------------------------------- */
    /* RELASI ELOQUENT                                                            */
    /* -------------------------------------------------------------------------- */

    /**
     * Relasi ke model WaktuKbm.
     * Jadwal pelajaran terikat pada satu konfigurasi slot waktu KBM (Many to One).
     */
    public function waktuKbm()
    {
        return $this->belongsTo(WaktuKbm::class, 'waktu_kbm_id');
    }

    /**
     * Relasi ke model Kelas.
     * Jadwal pelajaran diplot untuk satu ruang lingkup kelas tertentu (Many to One).
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Relasi ke model KodeGuru.
     * Jadwal pelajaran ini diampu oleh guru pengajar tertentu berdasarkan kode tugasnya (Many to One).
     */
    public function kodeGuru()
    {
        return $this->belongsTo(KodeGuru::class, 'kode_guru_id');
    }

    /**
     * Relasi ke model Ruangan (Modul Sarpras).
     * Menunjuk ruangan fisik tempat aktivitas pembelajaran berlangsung (Many to One).
     */
    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }
}