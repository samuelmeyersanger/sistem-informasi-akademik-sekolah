<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Loggable;

class CatatanPiketHarian extends Model
{
    use SoftDeletes, Loggable;

    protected $table = 'catatan_piket_harian';

    protected $fillable = [
        'tanggal',
        'catatan_kejadian',
        'pegawai_id' // Guru piket pelapor/pencatat kejadian
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * Relasi ke Guru Piket yang menginput/bertanggung jawab atas catatan harian ini
     */
    public function pembuatCatatan(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}