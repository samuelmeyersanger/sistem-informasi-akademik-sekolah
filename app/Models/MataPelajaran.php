<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\KodeGuru;
use App\Traits\Loggable;

class MataPelajaran extends Model
{
    use SoftDeletes, Loggable;

    /**
     * Nama tabel yang terikat dengan model ini.
     *
     * @var string
     */
    protected $table = 'mata_pelajaran';

    /**
     * Kolom-kolom yang diizinkan untuk diisi secara massal (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nomor_urut',
        'nama_mapel',
        'singkatan_mapel',
        'jumlah_jam',
    ];

    /**
     * Casting tipe data otomatis dari Laravel.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'nomor_urut' => 'integer',
        'jumlah_jam' => 'integer',
        'deleted_at' => 'datetime',
    ];

    /* -------------------------------------------------------------------------- */
    /* RELASI ELOQUENT                                                            */
    /* -------------------------------------------------------------------------- */

    /**
     * Relasi ke model KodeGuru.
     * Satu mata pelajaran dapat diampu oleh beberapa kode/entitas penugasan guru (One to Many).
     */
    public function kodeGuru()
    {
        return $this->hasMany(KodeGuru::class, 'mata_pelajaran_id');
    }
}