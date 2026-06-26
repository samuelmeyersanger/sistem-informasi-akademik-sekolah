<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokumenPegawai extends Model
{
    use SoftDeletes;

    protected $table = 'dokumen_pegawai';

    protected $fillable = [
        'pegawai_id',
        'jenis_dokumen',
        'nama_dokumen',
        'tahun_dokumen',
        'file_dokumen',
    ];

    // Kembali ke data Pegawai pemilik dokumen
    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}