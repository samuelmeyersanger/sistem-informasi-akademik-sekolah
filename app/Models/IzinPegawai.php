<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Loggable;

class IzinPegawai extends Model
{
    use SoftDeletes, Loggable;

    protected $table = 'izin_pegawai';

    protected $fillable = [
        'tanggal',
        'pegawai_id',
        'mata_pelajaran_id',
        'waktu_keluar',
        'waktu_kembali',
        'alasan_keluar',
        'invaler_id',
        'tanda_tangan_piket',
        'tanda_tangan_pegawai',
        'status'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * Relasi ke Pegawai yang meminta izin keluar
     */
    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    /**
     * Relasi ke Mata Pelajaran yang ditinggalkan saat jam izin tersebut
     */
    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }

    /**
     * Relasi ke Pegawai Invaler (Guru Pengganti/Piket yang mengisi kelas)
     */
    public function invaler(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'invaler_id');
    }
}