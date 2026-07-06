<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Loggable;

class Ekstrakurikuler extends Model
{
    use SoftDeletes, Loggable;

    // Definisikan nama tabel karena tidak menggunakan bahasa Inggris jamak
    protected $table = 'ekstrakurikuler';

    protected $fillable = [
        'nama',
        'pembina_id',
        'logo',
        'hari_latihan',
        'jam_mulai',
        'jam_selesai',
        'deskripsi',
        'is_aktif',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    /**
     * Relasi ke Pembina (Pegawai)
     */
    public function pembina(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'pembina_id');
    }

    /**
     * Relasi ke daftar Anggota Ekskul
     */
    public function anggota(): HasMany
    {
        return $this->hasMany(AnggotaEkstrakurikuler::class, 'ekstrakurikuler_id');
    }

    /**
     * Relasi ke seluruh riwayat Absensi Ekskul
     */
    public function absensi(): HasMany
    {
        return $this->hasMany(AbsensiEkstrakurikuler::class, 'ekstrakurikuler_id');
    }

    /**
     * Relasi ke daftar Prestasi Ekskul
     */
    public function prestasi(): HasMany
    {
        return $this->hasMany(PrestasiEkstrakurikuler::class, 'ekstrakurikuler_id');
    }

        /**
     * ========================================================================
     * SCOPE PENGAMAN AKSES PEMBINA EKSKUL
     * ========================================================================
     */
    public function scopeAksesPembina($query, $user)
    {
        // 1. Jika punya izin super admin, bisa lihat semua ekskul
        if ($user->hasPermission('akses-semua-ekskul')) {
            return $query;
        }
        // 2. Cek apakah user ini adalah pegawai
        $pegawai = \App\Models\Pegawai::where('user_id', $user->id)->first();
        if (!$pegawai) {
            // Jika bukan pegawai (atau tidak terhubung), sembunyikan semua data
            return $query->where('id', '<', 0);
        }
        // 3. Kunci data hanya untuk ekskul di mana dia menjadi pembinanya
        return $query->where('pembina_id', $pegawai->id);
    }
}