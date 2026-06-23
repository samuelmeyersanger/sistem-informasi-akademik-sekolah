<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Loggable;

class RiwayatStatusSiswa extends Model
{
    use SoftDeletes, Loggable;

    protected $table = 'riwayat_status_siswa';

    protected $fillable = [
        'siswa_id',
        'semester_id',
        'status',
        'metadata',
    ];

    /**
     * Otomatis mengubah JSON string dari DB menjadi Array PHP (dan sebaliknya)
     */
    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Relasi balik ke Siswa
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    /**
     * Relasi ke Semester saat perubahan status terjadi
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }
}