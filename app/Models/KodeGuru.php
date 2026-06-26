<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Pegawai;
use App\Models\MataPelajaran;
use App\Models\JadwalPelajaran;
use App\Traits\Loggable;

class KodeGuru extends Model
{
    use SoftDeletes, Loggable;

    /**
     * Nama tabel yang terikat dengan model ini.
     *
     * @var string
     */
    protected $table = 'kode_guru';

    /**
     * Kolom-kolom yang diizinkan untuk diisi secara massal (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'kode',
        'pegawai_id',
        'mata_pelajaran_id',
        'jumlah_jam_mengajar',
    ];

    /**
     * Casting tipe data otomatis dari Laravel.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'pegawai_id' => 'integer',
        'mata_pelajaran_id' => 'integer',
        'jumlah_jam_mengajar' => 'integer',
        'deleted_at' => 'datetime',
    ];

    /* -------------------------------------------------------------------------- */
    /* RELASI ELOQUENT                                                            */
    /* -------------------------------------------------------------------------- */

    /**
     * Relasi ke model Pegawai (Guru).
     * Setiap entitas kode guru mengikat satu data identitas pegawai (Many to One).
     */
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    /**
     * Relasi ke model MataPelajaran.
     * Penugasan kode guru ini ditujukan untuk mengampu satu spesifikasi mata pelajaran (Many to One).
     */
    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }

    /**
     * Relasi ke model JadwalPelajaran.
     * Satu kode guru dapat dialokasikan ke dalam banyak jadwal mengajar di kelas (One to Many).
     */
    public function jadwalPelajaran()
    {
        return $this->hasMany(JadwalPelajaran::class, 'kode_guru_id');
    }
}