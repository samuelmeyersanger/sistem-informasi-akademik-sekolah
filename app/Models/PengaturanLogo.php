<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PengaturanLogo extends Model
{
    // Mengaktifkan fitur Soft Deletes agar aset gambar penting tidak hilang permanen jika tidak sengaja terhapus
    use SoftDeletes;

    /**
     * Nama tabel yang terikat dengan model ini.
     * Wajib didefinisikan agar Laravel tidak mencari tabel bernama 'pengaturan_logos'.
     *
     * @var string
     */
    protected $table = 'pengaturan_logo';

    /**
     * Kolom-kolom yang diizinkan untuk diisi secara massal (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'logo_pemda',
        'logo_sekolah',
        'kop_surat',
        'ttd_kepala_sekolah',
        'stempel_sekolah',
        'ttd_dan_stempel',
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
    ];
}