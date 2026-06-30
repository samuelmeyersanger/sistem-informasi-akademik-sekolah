<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Loggable;


class PemanggilanOrangTua extends Model
{
    use SoftDeletes, Loggable;

    protected $table = 'pemanggilan_orang_tua';

    protected $fillable = [
        'tanggal_panggilan',
        'siswa_id',
        'wali_id',
        'alasan_panggilan',
        'status',
        'tanggal_kehadiran',
        'hasil_pertemuan',
        'pegawai_id',
    ];

    protected $casts = [
        'tanggal_panggilan' => 'date',
        'tanggal_kehadiran' => 'date',
    ];

    /**
     * Relasi ke Siswa yang bersangkutan
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    /**
     * Relasi ke Wali Murid yang dipanggil
     */
    public function wali(): BelongsTo
    {
        return $this->belongsTo(WaliSiswa::class, 'wali_id');
    }

    /**
     * Relasi ke Guru BK / Pegawai penghubung
     */
    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}