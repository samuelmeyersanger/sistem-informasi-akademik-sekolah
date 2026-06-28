<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Loggable;

class PrestasiEkstrakurikuler extends Model
{
    use SoftDeletes, Loggable;

    protected $table = 'prestasi_ekstrakurikuler';

    protected $fillable = [
        'ekstrakurikuler_id',
        'nama_prestasi',
        'tingkat',
        'juara',
        'tanggal_prestasi',
        'penyelenggara',
        'file_sertifikat',
        'file_dokumentasi',
    ];

    protected $casts = [
        'tanggal_prestasi' => 'date',
    ];

    /**
     * Relasi balik ke Ekskul peraih prestasi
     */
    public function ekstrakurikuler(): BelongsTo
    {
        return $this->belongsTo(Ekstrakurikuler::class, 'ekstrakurikuler_id');
    }
}