<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Loggable;

class Menu extends Model
{
    use HasFactory, Loggable;

    // 1. Beritahu Laravel nama tabel yang kita buat di migration tadi
    protected $table = 'menus';

    // 2. Daftarkan kolom-kolom yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'kategori',
        'nama_menu',
        'url',
        'icon',
        'urutan',
        'permission_slug',
    ];

    // 3. Opsional: Mengubah tipe data 'urutan' otomatis menjadi integer saat dibaca PHP
    protected $casts = [
        'urutan' => 'integer',
    ];
}