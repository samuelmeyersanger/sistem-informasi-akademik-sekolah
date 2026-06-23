<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\Loggable;

class Siswa extends Model
{
    use SoftDeletes, Loggable;

    protected $table = 'siswa';

    protected $fillable = [
        'user_id',
        'semester_id',
        'nama_lengkap',
        'nipd',
        'jenis_kelamin',
        'nisn',
        'tempat_lahir',
        'tanggal_lahir',
        'nik',
        'agama',
        'provinsi',        // Tambahan: Diperlukan untuk integrasi Laravolt
        'kota',            // Tambahan: Diperlukan untuk integrasi Laravolt
        'kecamatan',
        'kelurahan_desa',
        'alamat_lengkap',
        'rt',
        'rw',
        'kode_pos',
        'nomor_hp',
        'no_peserta_un',
        'asal_sekolah',
        'anak_ke',
        'tingkat',
        'diterima_pada_tanggal',
        'status_siswa',
        'kelas_id',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'diterima_pada_tanggal' => 'date',
    ];

    /**
     * Hubungan ke User (Akun login siswa jika ada)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Hubungan ke Semester saat mendaftar/aktif
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    /**
     * Hubungan ke Kelas
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Relasi Many-to-Many ke data Wali Murid (Orang Tua / Wali Asuh)
     */
    public function wali(): BelongsToMany
    {
        return $this->belongsToMany(WaliSiswa::class, 'siswa_wali', 'siswa_id', 'wali_siswa_id')
                    ->withPivot('hubungan')
                    ->withTimestamps();
    }
    /**
     * Relasi ke data Anggota Kelas Aktif
     */
    public function anggotaKelas(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AnggotaKelas::class, 'siswa_id');
    }

    /**
     * Relasi ke seluruh rekam jejak Riwayat Kelas Siswa (One-to-Many)
     */
    public function riwayatKelas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(RiwayatKelasSiswa::class, 'siswa_id');
    }

    /**
     * Relasi ke seluruh rekam jejak Riwayat Status Operasional Siswa (One-to-Many)
     */
    public function riwayatStatus(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(RiwayatStatusSiswa::class, 'siswa_id');
    }

    /**
     * Relasi ke berkas berkas lampiran digital siswa (One-to-Many)
     */
    public function dokumen(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DokumenSiswa::class, 'siswa_id');
    }
}