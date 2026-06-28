<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pegawai extends Model
{
    use SoftDeletes;

    protected $table = 'pegawai';

    protected $fillable = [
        'user_id',
        'semester_id',
        'nama_lengkap',
        'jenis_kelamin',
        'nip',
        'nuptk',
        'status_pegawai',
        'pangkat_golongan',
        'jenis_ptk',
        'status_keaktifan',
        'email',
        'tanggal_mutasi',
        'alasan_mutasi',
        'sekolah_tujuan',
        'file_surat_mutasi',
        'tanggal_pensiun',
        'file_surat_pensiun',
    ];

    protected $casts = [
        'tanggal_mutasi' => 'date',
        'tanggal_pensiun' => 'date',
    ];

    // Berelasi ke User (jika pegawai punya akun login)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Berelasi ke Semester
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    // Punya banyak Dokumen
    public function dokumen(): HasMany
    {
        return $this->hasMany(DokumenPegawai::class, 'pegawai_id');
    }

    // Punya banyak Riwayat Kenaikan Gaji Berkala (KGB)
    public function kgb(): HasMany
    {
        return $this->hasMany(KenaikanGajiBerkala::class, 'pegawai_id');
    }

    // Punya banyak Riwayat Kenaikan Pangkat
    public function kenaikanPangkat(): HasMany
    {
        return $this->hasMany(KenaikanPangkat::class, 'pegawai_id');
    }

    public function ekskulBinaan() 
    {
        return $this->hasMany(Ekstrakurikuler::class, 'pembina_id');
    }
}