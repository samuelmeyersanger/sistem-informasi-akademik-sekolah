<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Loggable;

class KelasWali extends Model
{
    use SoftDeletes, Loggable;

    // Menghubungkan model ini ke tabel kelas_wali
    protected $table = 'kelas_wali';

    // Kolom-kolom yang boleh diisi secara massal
    protected $fillable = [
        'nama_kelas',
        'tingkat',
        'wali_kelas_id',
        'semester_id',
        'jumlah_siswa',
    ];

    /**
     * RELASI ELOQUENT
     */
     
    // Relasi ke tabel Pegawai (sebagai pembimbing/wali)
    public function waliKelas()
    {
        return $this->belongsTo(Pegawai::class, 'wali_kelas_id');
    }

    // Relasi ke tabel Semester aktif
    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    // Relasi turun ke bawah (Satu Kelas Wali punya BANYAK Anggota Kelas)
    public function anggota()
    {
        return $this->hasMany(AnggotaKelasWali::class, 'kelas_wali_id');
    }
}