<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Loggable;


class TujuanPembelajaran extends Model
{
    use HasFactory, SoftDeletes, Loggable;

    /**
     * Nama tabel yang terhubung dengan model ini.
     *
     * @var string
     */
    protected $table = 'tujuan_pembelajaran';

    /**
     * Atribut yang diizinkan untuk diisi secara massal (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'mata_pelajaran_id',
        'tingkat',
        'nomor_tujuan',
        'deskripsi',
    ];

    /**
     * Relasi ke tabel Mata Pelajaran.
     * (Setiap Tujuan Pembelajaran dimiliki oleh 1 Mata Pelajaran tertentu)
     */
    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }
}