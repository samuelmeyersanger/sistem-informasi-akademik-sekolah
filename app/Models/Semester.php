<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\TahunAjaran;
use App\Traits\Loggable;

class Semester extends Model
{
    // Mengaktifkan fitur Soft Deletes sesuai dengan migration Anda
    use SoftDeletes, Loggable;

    /**
     * Nama tabel yang terikat dengan model ini.
     * Wajib didefinisikan agar Laravel tidak mencari tabel bernama 'semesters'.
     *
     * @var string
     */
    protected $table = 'semester';

    /**
     * Kolom-kolom yang diizinkan untuk diisi secara massal (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'semester_ke',
        'tahun_ajaran_id',
        'is_aktif',
    ];

    /**
     * Casting tipe data otomatis dari Laravel.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'semester_ke' => 'integer',
        'is_aktif' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Scope untuk mempermudah mengambil Semester yang sedang Aktif saat ini.
     * Cara pakai di Controller: $semesterAktif = Semester::active()->first();
     */
    public function scopeActive($query)
    {
        return $query->where('is_aktif', true);
    }

    /* -------------------------------------------------------------------------- */
    /* RELASI ELOQUENT                                                            */
    /* -------------------------------------------------------------------------- */

    /**
     * Relasi ke model TahunAjaran
     * Satu semester berada di dalam satu Tahun Ajaran (Many to One)
     */
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }
}