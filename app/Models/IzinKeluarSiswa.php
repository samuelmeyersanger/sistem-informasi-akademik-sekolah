<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Loggable;

class IzinKeluarSiswa extends Model
{
    use SoftDeletes, Loggable;

    protected $table = 'izin_keluar_siswa';

    protected $fillable = [
        'tanggal',
        'kelas_id',
        'siswa_id',
        'waktu_keluar',
        'waktu_kembali',
        'alasan_keluar',
        'tanda_tangan_piket',
        'tanda_tangan_siswa',
        'status'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}