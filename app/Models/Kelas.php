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

        /**
     * ========================================================================
     * SCOPE PENGAMAN KELAS & JADWAL
     * ========================================================================
     */
    public function scopeAksesSesuaiWali($query, $user)
    {
        // 1. Izin khusus untuk Admin (harus dibuat di tabel Permission nanti)
        if ($user->hasPermission('akses-semua-kelas')) {
            return $query;
        }
        // 2. Cek apakah user adalah Pegawai
        $pegawai = \App\Models\Pegawai::where('user_id', $user->id)->first();
        if ($pegawai) {
            // 3. Filter kelas hanya di mana pegawai ini menjadi wali kelasnya
            return $query->where('wali_kelas_id', $pegawai->id);
        }
        // 4. Tolak akses jika bukan keduanya
        return $query->where('id', 0);
    }
        /**
     * Relasi ke Anggota Kelas (Untuk menghitung rekap jumlah siswa per kelas)
     */
    public function anggotaKelas()
    {
        return $this->hasMany(AnggotaKelas::class, 'kelas_id', 'id');
    }
}