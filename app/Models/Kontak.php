<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kontak extends Model
{
    /**
     * Nama tabel yang terikat dengan model ini.
     * Wajib didefinisikan agar Laravel tidak mencari tabel bernama 'kontaks'.
     *
     * @var string
     */
    protected $table = 'kontak';

    /**
     * Kolom-kolom yang diizinkan untuk diisi secara massal (Mass Assignment).
     * Sangat aman untuk menampung inputan request dari form publik.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'email',
        'subject',
        'pesan',
    ];

    /**
     * Casting tipe data otomatis untuk tanggal pembuatan pesan.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}