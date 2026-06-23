<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Loggable; // Jika Anda menggunakan sistem logging global

class AnggotaKelas extends Model
{
    use SoftDeletes, Loggable;

    protected $table = 'anggota_kelas';

    protected $fillable = [
        'kelas_id',
        'tingkat',
        'siswa_id',
        'semester_id',
    ];

    /**
     * Relasi ke data Master Siswa
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    /**
     * Relasi ke data Master Kelas
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Relasi ke data Akademik Semester
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }
}