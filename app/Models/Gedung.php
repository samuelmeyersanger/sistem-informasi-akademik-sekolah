<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Loggable;

class Gedung extends Model
{
    use SoftDeletes, Loggable;

    protected $table = 'gedung';

    protected $fillable = [
        'nama_gedung',
        'kode_gedung',
        'deskripsi',
        'jumlah_lantai'
    ];

    /**
     * Hubungan ke tabel Ruangan (Satu gedung memiliki banyak ruangan)
     */
    public function ruangan(): HasMany
    {
        return $this->hasMany(Ruangan::class, 'gedung_id');
    }

    /**
     * Hubungan langsung ke tabel Inventaris (Satu gedung menampung banyak barang inventaris)
     */
    public function inventaris(): HasMany
    {
        return $this->hasMany(Inventaris::class, 'gedung_id');
    }
}