<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Loggable;

class JurnalHarianBk extends Model
{
    use SoftDeletes, Loggable;

    protected $table = 'jurnal_harian_bk';

    protected $fillable = [
        'tanggal',
        'pegawai_id',
        'kelas_id',
        'minggu_ke',
        'sasaran_kegiatan',
        'kegiatan_layanan',
        'hasil',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * Relasi ke Guru BK (Pegawai)
     */
    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    /**
     * Relasi ke Kelas
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
}