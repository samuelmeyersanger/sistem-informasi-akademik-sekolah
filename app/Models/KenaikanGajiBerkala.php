<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Loggable;

class KenaikanGajiBerkala extends Model
{
    use SoftDeletes, Loggable;

    protected $table = 'kenaikan_gaji_berkala';

    protected $fillable = [
        'pegawai_id',
        'nomor_sk_kgb',
        'tanggal_sk_kgb',
        'nominal_gaji_baru',
    ];

    protected $casts = [
        'tanggal_sk_kgb' => 'date',
        'nominal_gaji_baru' => 'float', // Mempermudah format angka di view PHP
    ];

    // Kembali ke data Pegawai
    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}