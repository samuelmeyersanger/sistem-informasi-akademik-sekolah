<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Loggable;

class AnggotaKelasWali extends Model
{
    use SoftDeletes, Loggable;

    // Menghubungkan model ini ke tabel anggota_kelas_wali
    protected $table = 'anggota_kelas_wali';

    // Kolom-kolom yang boleh diisi secara massal
    protected $fillable = [
        'kelas_wali_id',
        'tingkat',
        'siswa_id',
        'semester_id',
    ];

    /**
     * RELASI ELOQUENT
     */

    // Relasi naik ke atas (Merujuk ke Kelas Wali induknya)
    public function kelasWali()
    {
        return $this->belongsTo(KelasWali::class, 'kelas_wali_id');
    }

    // Relasi ke tabel Siswa (Untuk menarik nama lengkap siswa, NISN, dll)
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    // Relasi ke tabel Semester aktif
    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }
}