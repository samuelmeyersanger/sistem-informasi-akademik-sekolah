<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Loggable;

class KenaikanPangkat extends Model
{
    use SoftDeletes, Loggable;

    protected $table = 'kenaikan_pangkat';

    protected $fillable = [
        'pegawai_id',
        'nomor_sk_kp',
        'tanggal_sk_kp',
        'pangkat_golongan_baru',
    ];

    protected $casts = [
        'tanggal_sk_kp' => 'date',
    ];

    // Kembali ke data Pegawai
    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}