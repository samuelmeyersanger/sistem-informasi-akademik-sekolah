<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Loggable;

class DokumenSiswa extends Model
{
    use SoftDeletes, Loggable;

    protected $table = 'dokumen_siswa';

    protected $fillable = [
        'siswa_id',
        'jenis_dokumen',
        'nama_dokumen',
        'tahun_dokumen',
        'file_dokumen',
    ];

    /**
     * Relasi balik ke pemilik Dokumen (Siswa)
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}