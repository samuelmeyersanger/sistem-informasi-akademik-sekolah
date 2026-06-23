<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Loggable;

class Tentang extends Model
{
    // Mengaktifkan fitur Soft Deletes sesuai dengan isi migration Anda ($table->softDeletes())
    use SoftDeletes, Loggable;

    /**
     * Nama tabel yang terikat dengan model ini.
     * Secara default Laravel akan mencari tabel bernama 'tentangs', 
     * jadi kita harus mendefinisikannya secara manual agar mengarah ke tabel 'tentang'.
     *
     * @var string
     */
    protected $table = 'tentang';

    /**
     * Kolom-kolom yang diizinkan untuk diisi secara massal (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'gambar',
        'judul',
        'deskripsi',
        'tombol_teks',
        'tombol_url',
        'video_url',
    ];

    /**
     * Kolom yang harus diubah tipenya secara otomatis oleh Laravel (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}