<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrestasiSiswa extends Model
{
    use SoftDeletes;

    protected $table = 'prestasi_siswa';

    protected $fillable = [
        'siswa_id',
        'kelas_id',
        'jenis_prestasi',
        'nama_prestasi',
        'tahun_prestasi',
        'file_sertifikat'
    ];

    // Hubungkan prestasi ke data Siswa
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    // Hubungkan prestasi ke data Kelas saat ia meraihnya
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
}