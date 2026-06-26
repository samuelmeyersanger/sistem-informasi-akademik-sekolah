<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Loggable;

class PeminjamanSarpras extends Model
{
    use SoftDeletes, Loggable;

    protected $table = 'peminjaman_sarpras';

    protected $fillable = [
        'inventaris_id',
        'peminjam_id',
        'tanggal_pinjam',
        'tanggal_kembali_rencana',
        'tanggal_kembali_realisasi',
        'status',
        'keperluan',
        'catatan',
        'pegawai_id_pencatat'
    ];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_kembali_rencana' => 'date',
        'tanggal_kembali_realisasi' => 'date',
    ];

    /**
     * Hubungan ke tabel Inventaris (Peminjaman ini meminjam barang apa)
     */
    public function inventaris(): BelongsTo
    {
        return $this->belongsTo(Inventaris::class, 'inventaris_id');
    }

    /**
     * Hubungan ke tabel Pegawai selaku Peminjam (Siapa pegawai/guru yang meminjam barang)
     */
    public function peminjam(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'peminjam_id');
    }

    /**
     * Hubungan ke tabel Pegawai selaku Pencatat (Siapa staf/sarpras yang mencatat transaksi peminjaman ini)
     */
    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id_pencatat');
    }
}