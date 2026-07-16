<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Loggable;

class NilaiEkstrakurikuler extends Model
{
    use HasFactory, SoftDeletes, Loggable;

    /**
     * Nama tabel yang terhubung dengan model ini.
     *
     * @var string
     */
    protected $table = 'nilai_ekstrakurikuler';

    /**
     * Atribut yang diizinkan untuk diisi secara massal (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ekstrakurikuler_id',
        'siswa_id',
        'predikat',
        'deskripsi',
    ];

    /**
     * Relasi ke tabel Ekstrakurikuler.
     */
    public function ekstrakurikuler()
    {
        return $this->belongsTo(Ekstrakurikuler::class, 'ekstrakurikuler_id');
    }

    /**
     * Relasi ke tabel Siswa.
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}