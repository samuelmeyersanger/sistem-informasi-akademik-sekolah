<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Loggable;

class Kelas extends Model
{
    use SoftDeletes, Loggable;

    protected $table = 'kelas';

    protected $fillable = [
        'nama_kelas',
        'tingkat',
        'wali_kelas_id',
        'semester_id',
        'jumlah_siswa',
    ];

    /**
     * Relasi ke data Siswa (Satu kelas punya banyak siswa)
     */
    public function siswa(): HasMany
    {
        return $this->hasMany(Siswa::class, 'kelas_id');
    }

    /**
     * Relasi ke data Pegawai / Guru (Wali Kelas)
     */
    public function waliKelas(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'wali_kelas_id'); // Sesuaikan nama model Pegawai Anda
    }

    /**
     * Relasi ke data Semester
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }
}