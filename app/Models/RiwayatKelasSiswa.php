<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Loggable;

class RiwayatKelasSiswa extends Model
{
    use SoftDeletes, Loggable;

    protected $table = 'riwayat_kelas_siswa';

    protected $fillable = [
        'siswa_id',
        'kelas_id',
        'tingkat',
        'semester_id',
        'keterangan',
    ];

    /**
     * Relasi balik ke Siswa
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    /**
     * Relasi ke Kelas saat log ini dicatat
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Relasi ke Semester saat log ini dicatat
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }
}