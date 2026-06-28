<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Loggable;

class KetidakhadiranPegawai extends Model
{
    use SoftDeletes, Loggable;

    protected $table = 'ketidakhadiran_pegawai';

    protected $fillable = [
        'tanggal',
        'pegawai_id',
        'mata_pelajaran_id',
        'keterangan', // Sakit, Izin, Alpha
        'tindak_lanjut'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }
}