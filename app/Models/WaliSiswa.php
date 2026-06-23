<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WaliSiswa extends Model
{
    use SoftDeletes;

    protected $table = 'wali_siswa';

    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'nik',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'pendidikan_terakhir',
        'pekerjaan',
        'penghasilan_bulanan',
        'alamat_lengkap',
        'rt',
        'rw',
        'kelurahan_desa',
        'kecamatan',
        'kota',
        'provinsi',
        'kode_pos',
        'nomor_hp',
        'email',
        'nomor_hp_darurat',
        'catatan',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'penghasilan_bulanan' => 'decimal:2',
    ];

    /**
     * Relasi ke akun User (jika ada)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi Many-to-Many ke data Siswa (Anak Didik / Anak Asuh)
     */
    public function anak(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Siswa::class, 'siswa_wali', 'wali_siswa_id', 'siswa_id')
                    ->withPivot('hubungan')
                    ->withTimestamps();
    }
}