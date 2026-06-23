<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Loggable;

class Pegawai extends Model
{
    use HasFactory, SoftDeletes, Loggable;

    // Nama tabel disesuaikan dengan migration Anda (plural/singular)
    protected $table = 'pegawai';

    protected $fillable = [
        'user_id',
        'semester_id',
        'nama_lengkap',
        'jenis_kelamin',
        'nip',
        'nuptk',
        'status_pegawai',
        'pangkat_golongan',
        'jenis_ptk',
        'status_keaktifan',
        'email',
        'tanggal_mutasi',
        'alasan_mutasi',
        'sekolah_tujuan',
        'file_surat_mutasi',
        'tanggal_pensiun',
        'file_surat_pensiun',
    ];

    protected $casts = [
        'tanggal_mutasi' => 'date',
        'tanggal_pensiun' => 'date',
    ];

    /**
     * Relasi ke model User (One to One / BelongsTo)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke model Semester (BelongsTo)
     */
    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }
}