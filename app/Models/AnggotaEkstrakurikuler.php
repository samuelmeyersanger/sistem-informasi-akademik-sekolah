<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Loggable;

class AnggotaEkstrakurikuler extends Model
{
    use SoftDeletes, Loggable;

    protected $table = 'anggota_ekstrakurikuler';

    protected $fillable = [
        'ekstrakurikuler_id',
        'siswa_id',
        'kelas_id',
        'nomor_hp',
        'motivasi',
        'tanggal_bergabung',
        'status',
    ];

    protected $casts = [
        'tanggal_bergabung' => 'date',
    ];

    /**
     * Relasi balik ke induk Ekskul
     */
    public function ekstrakurikuler(): BelongsTo
    {
        return $this->belongsTo(Ekstrakurikuler::class, 'ekstrakurikuler_id');
    }

    /**
     * Relasi ke data Siswa
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    /**
     * Relasi ke data Kelas saat siswa bergabung
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Relasi ke riwayat absensi siswa ini di ekskul terkait
     */
    public function riwayatAbsensi(): HasMany
    {
        return $this->hasMany(AbsensiEkstrakurikuler::class, 'anggota_ekstrakurikuler_id');
    }
}