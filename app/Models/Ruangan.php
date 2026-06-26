<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ruangan extends Model
{
    use SoftDeletes;

    protected $table = 'ruangan';

    protected $fillable = [
        'nama_ruangan',
        'kode_ruangan',
        'gedung_id',
        'kapasitas'
    ];

    /**
     * Hubungan ke tabel Gedung (Ruangan ini berada di gedung mana)
     */
    public function gedung(): BelongsTo
    {
        return $this->belongsTo(Gedung::class, 'gedung_id');
    }

    /**
     * Hubungan ke tabel Inventaris (Satu ruangan menyimpan banyak barang inventaris)
     */
    public function inventaris(): HasMany
    {
        return $this->hasMany(Inventaris::class, 'ruangan_id');
    }
}