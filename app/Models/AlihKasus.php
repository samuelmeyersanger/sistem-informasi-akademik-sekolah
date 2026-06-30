<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Loggable;

class AlihKasus extends Model
{
    use SoftDeletes, Loggable;

    protected $table = 'alih_kasus';

    protected $fillable = [
        'siswa_id',
        'topik_permasalahan',
        'bidang_bimbingan',
        'jenis_kegiatan',
        'fungsi_kegiatan',
        'tujuan_kegiatan',
        'hasil_yang_dicapai',
        'gambaran_ringkas_masalah',
        'alasan_alih_kasus',
        'jenis_alih',
        'kepada_siapa',
        'tanggal_alih',
        'bahan_disertakan',
        'keterkaitan_layanan_terdahulu',
        'rencana_penilaian_tindak_lanjut',
        'catatan',
    ];

    protected $casts = [
        'tanggal_alih' => 'date',
    ];

    /**
     * Relasi ke Siswa bermasalah
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}