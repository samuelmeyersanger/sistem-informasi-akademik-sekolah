<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Loggable;

class Inventaris extends Model
{
    use SoftDeletes, Loggable;

    protected $table = 'inventaris';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'deskripsi',
        'kategori',
        'merek',
        'model',
        'tahun_pembelian',
        'harga_perolehan',
        'kondisi',
        'lokasi',
        'ruangan_id',
        'gedung_id',
        'jumlah',
        'foto_barang',
        'tanggal_penghapusan',
        'alasan_penghapusan'
    ];

    protected $casts = [
        'harga_perolehan' => 'decimal:2',
        'tanggal_penghapusan' => 'date',
        'jumlah' => 'integer'
    ];

    /**
     * Hubungan ke tabel Gedung (Barang ini ditempatkan di gedung mana)
     */
    public function gedung(): BelongsTo
    {
        return $this->belongsTo(Gedung::class, 'gedung_id');
    }

    /**
     * Hubungan ke tabel Ruangan (Barang ini diletakkan di ruangan mana secara spesifik)
     */
    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    /**
     * Hubungan ke tabel Peminjaman (Barang inventaris ini memiliki banyak riwayat peminjaman)
     */
    public function peminjaman(): HasMany
    {
        return $this->hasMany(PeminjamanSarpras::class, 'inventaris_id');
    }
}