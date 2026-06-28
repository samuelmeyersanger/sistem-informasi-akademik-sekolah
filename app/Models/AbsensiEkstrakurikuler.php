<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Loggable;

class AbsensiEkstrakurikuler extends Model
{
    use SoftDeletes, Loggable;

    protected $table = 'absensi_ekstrakurikuler';

    protected $fillable = [
        'tanggal',
        'anggota_ekstrakurikuler_id',
        'ekstrakurikuler_id',
        'status',
        'tanda_tangan',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * Relasi balik ke data Anggota Ekskul yang diabsen
     */
    public function anggotaEkskul(): BelongsTo
    {
        return $this->belongsTo(AnggotaEkstrakurikuler::class, 'anggota_ekstrakurikuler_id');
    }

    /**
     * Relasi pendukung langsung ke induk Ekskul (Biar query rekap per ekskul lebih cepat)
     */
    public function ekstrakurikuler(): BelongsTo
    {
        return $this->belongsTo(Ekstrakurikuler::class, 'ekstrakurikuler_id');
    }
}