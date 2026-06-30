<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiswaTerlambat extends Model
{
    use SoftDeletes;

    protected $table = 'siswa_terlambat';

    protected $fillable = [
        'tanggal',
        'siswa_id',
        'kelas_id',
        'jam_masuk',
        'menit_terlambat',
        'alasan',
        'tindak_lanjut',
        'pegawai_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'menit_terlambat' => 'integer',
    ];

    /**
     * Relasi ke Siswa
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    /**
     * Relasi ke Kelas aktif siswa
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Relasi ke Guru Piket / Pegawai yang menginput
     */
    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}