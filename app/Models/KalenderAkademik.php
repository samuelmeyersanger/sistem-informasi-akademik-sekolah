<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Traits\Loggable;

class KalenderAkademik extends Model
{
    use SoftDeletes, Loggable;

    /**
     * Nama tabel yang terikat dengan model ini.
     *
     * @var string
     */
    protected $table = 'kalender_akademik';

    /**
     * Kolom-kolom yang diizinkan untuk diisi secara massal (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_kegiatan',
        'jenis',
        'tanggal_mulai',
        'tanggal_selesai',
        'deskripsi',
        'tahun_ajaran_id',
        'semester_id',
    ];

    /**
     * Casting tipe data otomatis dari Laravel.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'tahun_ajaran_id' => 'integer',
        'semester_id' => 'integer',
        'deleted_at' => 'datetime',
    ];

    /* -------------------------------------------------------------------------- */
    /* RELASI ELOQUENT                                                            */
    /* -------------------------------------------------------------------------- */

    /**
     * Relasi ke model TahunAjaran.
     * Agenda kalender akademik merujuk pada tahun ajaran yang sedang berjalan (Many to One).
     */
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    /**
     * Relasi ke model Semester.
     * Agenda kalender akademik diposisikan dalam klasifikasi semester genap/ganjil tertentu (Many to One).
     */
    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }
}