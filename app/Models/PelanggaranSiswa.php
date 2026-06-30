<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Loggable;

class PelanggaranSiswa extends Model
{
    use SoftDeletes, Loggable;

    protected $table = 'pelanggaran_siswa';

    protected $fillable = [
        'tanggal',
        'siswa_id',
        'kelas_id',
        'kategori',
        'jenis_pelanggaran',
        'deskripsi',
        'poin',
        'tindak_lanjut',
        'pegawai_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'poin' => 'integer',
    ];

    /**
     * Relasi ke Siswa
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    /**
     * Relasi ke Kelas saat pelanggaran terjadi
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Relasi ke Guru/Pegawai yang mencatat kasus
     */
    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}