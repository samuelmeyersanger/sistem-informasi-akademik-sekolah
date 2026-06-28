<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Loggable;

class PetugasPiket extends Model
{
    use SoftDeletes, Loggable;

    protected $table = 'petugas_piket';

    protected $fillable = [
        'hari',
        'penanggung_jawab_id',
        'anggota_piket', // Akan menyimpan array dari pegawai_id, contoh: [1, 4, 12]
        'tahun_ajaran_id',
        'semester_id'
    ];

    /**
     * Casting kolom JSON menjadi array PHP secara otomatis
     */
    protected $casts = [
        'anggota_piket' => 'array',
    ];

    /**
     * Relasi ke Guru/Pegawai yang menjadi Penanggung Jawab Piket hari itu
     */
    public function penanggungJawab(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'penanggung_jawab_id');
    }

    /**
     * Relasi ke Tahun Ajaran aktif saat piket bertugas
     */
    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    /**
     * Relasi ke Semester aktif saat piket bertugas
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    /**
     * Helper tambahan untuk mengambil objek model Pegawai dari array JSON anggota_piket
     */
    public function getObjekAnggotaPiketAttribute()
    {
        if (empty($this->anggota_piket)) {
            return collect();
        }
        return Pegawai::whereIn('id', $this->anggota_piket)->get();
    }
}