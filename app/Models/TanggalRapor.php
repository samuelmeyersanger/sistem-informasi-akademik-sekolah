<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Loggable;

class TanggalRapor extends Model
{
    use HasFactory, SoftDeletes, Loggable;

    // Menentukan nama tabel secara eksplisit
    protected $table = 'tanggal_rapor';

    // Kolom-kolom yang diizinkan untuk diisi massal (Mass Assignment)
    protected $fillable = [
        'tahun_ajaran_id',
        'semester_id',
        'tempat_cetak',
        'tanggal_cetak',
        'nama_kepala_sekolah',
        'nip_kepala_sekolah',
        'label_kepala_sekolah',
        'label_nip_kepala_sekolah',
        'label_nip_wali_kelas',
    ];

    // Memastikan Laravel mengenali tanggal_cetak sebagai tipe tanggal (Carbon)
    protected $casts = [
        'tanggal_cetak' => 'date',
    ];

    /**
     * Relasi ke tabel Tahun Ajaran
     */
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id', 'id');
    }

    /**
     * Relasi ke tabel Semester
     */
    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id', 'id');
    }

    /**
     * Scope untuk mendapatkan pengaturan rapor di semester yang sedang Aktif (jalan pintas)
     */
    public function scopeAktif($query)
    {
        return $query->whereHas('tahunAjaran', function ($q) {
            $q->where('is_active', true);
        })->whereHas('semester', function ($q) {
            $q->where('is_active', true);
        });
    }
}