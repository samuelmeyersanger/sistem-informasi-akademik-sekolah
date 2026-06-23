<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Loggable;

class TahunAjaran extends Model
{
    // Mengaktifkan fitur Soft Deletes agar data history akademik masa lalu aman dari terhapus tidak sengaja
    use SoftDeletes, Loggable;

    /**
     * Nama tabel yang terikat dengan model ini.
     * Wajib didefinisikan agar Laravel tidak mencari tabel bernama 'tahun_ajarans'.
     *
     * @var string
     */
    protected $table = 'tahun_ajaran';

    /**
     * Kolom-kolom yang diizinkan untuk diisi secara massal (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_tahun_ajaran',
        'is_aktif',
    ];

    /**
     * Casting tipe data otomatis dari Laravel.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_aktif' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Scope untuk mempermudah mengambil Tahun Ajaran yang sedang Aktif saat ini.
     * Cara pakai di Controller: $tahunAktif = TahunAjaran::active()->first();
     */
    public function scopeActive($query)
    {
        return $query->where('is_aktif', true);
    }

    /**
     * Relasi ke model Semester
     * Satu tahun ajaran bisa memiliki beberapa semester (biasanya Ganjil dan Genap) (One to Many)
     */
    public function semesters()
    {
        return $this->hasMany(Semester::class, 'tahun_ajaran_id');
    }
    
}