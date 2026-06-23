<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Loggable;

class ProfilSekolah extends Model
{
    // Mengaktifkan fitur Soft Deletes sesuai dengan isi migration Anda
    use SoftDeletes, Loggable;

    /**
     * Nama tabel yang terikat dengan model ini.
     * Wajib didefinisikan agar Laravel tidak mencari tabel bernama 'profil_sekolahs'.
     *
     * @var string
     */
    protected $table = 'profil_sekolah';

    /**
     * Kolom-kolom yang diizinkan untuk diisi secara massal (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_sekolah',
        'jenjang',
        'fase',
        'npsn',
        'nss',
        'provinsi',
        'kota',
        'kecamatan',
        'kelurahan',
        'alamat',
        'kode_pos',
        'latitude',
        'longitude',
        'website',
        'email',
    ];

    /**
     * Casting tipe data otomatis dari Laravel.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'latitude'  => 'float',
        'longitude' => 'float',
    ];
}