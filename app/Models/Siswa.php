<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\Loggable;

class Siswa extends Model
{
    use SoftDeletes, Loggable;

    protected $table = 'siswa';

    protected $fillable = [
        'user_id',
        'semester_id',
        'nama_lengkap',
        'nipd',
        'jenis_kelamin',
        'nisn',
        'tempat_lahir',
        'tanggal_lahir',
        'nik',
        'agama',
        'provinsi',        // Tambahan: Diperlukan untuk integrasi Laravolt
        'kota',            // Tambahan: Diperlukan untuk integrasi Laravolt
        'kecamatan',
        'kelurahan_desa',
        'alamat_lengkap',
        'rt',
        'rw',
        'kode_pos',
        'nomor_hp',
        'no_peserta_un',
        'asal_sekolah',
        'anak_ke',
        'tingkat',
        'diterima_pada_tanggal',
        'status_siswa',
        'kelas_id',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'diterima_pada_tanggal' => 'date',
    ];

    /**
     * Hubungan ke User (Akun login siswa jika ada)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Hubungan ke Semester saat mendaftar/aktif
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    /**
     * Hubungan ke Kelas
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Relasi Many-to-Many ke data Wali Murid (Orang Tua / Wali Asuh)
     */
    public function wali()
    {
        // Parameter ke-2 adalah nama tabel asli kamu di database jika bukan 'siswa_wali'
        return $this->belongsToMany(WaliSiswa::class, 'siswa_wali', 'siswa_id', 'wali_siswa_id')
                    ->withPivot('hubungan')
                    ->withTimestamps();
    }
    /**
     * Relasi ke data Anggota Kelas Aktif
     */
    public function anggotaKelas(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AnggotaKelas::class, 'siswa_id');
    }

    /**
     * Relasi ke seluruh rekam jejak Riwayat Kelas Siswa (One-to-Many)
     */
    public function riwayatKelas(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(RiwayatKelasSiswa::class, 'siswa_id');
    }

    /**
     * Relasi ke seluruh rekam jejak Riwayat Status Operasional Siswa (One-to-Many)
     */
    public function riwayatStatus(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(RiwayatStatusSiswa::class, 'siswa_id');
    }

    /**
     * Relasi ke berkas berkas lampiran digital siswa (One-to-Many)
     */
    public function dokumen(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DokumenSiswa::class, 'siswa_id');
    }

    public function prestasi() 
    {
        return $this->hasMany(PrestasiSiswa::class, 'siswa_id');
    }

    public function ekskulYangDiikuti() 
    {
        return $this->hasMany(AnggotaEkstrakurikuler::class, 'siswa_id');
    }

    public function pelanggaran() {
        return $this->hasMany(PelanggaranSiswa::class, 'siswa_id');
    }

    public function keterlambatan() {
        return $this->hasMany(SiswaTerlambat::class, 'siswa_id');
    }

        // =====================================================
    // PERBAIKAN RELASI LARAVOLT INDONESIA
    // =====================================================
    public function provinsi_relasi()
    {
        // Ubah ujungnya dari 'id' menjadi 'name'
        return $this->belongsTo(\Laravolt\Indonesia\Models\Province::class, 'provinsi', 'name');
    }
    public function kota_relasi()
    {
        // Ubah ujungnya dari 'id' menjadi 'name'
        return $this->belongsTo(\Laravolt\Indonesia\Models\City::class, 'kota', 'name');
    }
    public function kecamatan_relasi()
    {
        // Ubah ujungnya dari 'id' menjadi 'name'
        return $this->belongsTo(\Laravolt\Indonesia\Models\District::class, 'kecamatan', 'name');
    }
    public function kelurahan_relasi()
    {
        // Ubah ujungnya dari 'id' menjadi 'name'
        return $this->belongsTo(\Laravolt\Indonesia\Models\Village::class, 'kelurahan_desa', 'name');
    }

    // =====================================================
    // ACCESSOR untuk JSON export ke form Edit
    // =====================================================
    protected $appends = ['ayah_data', 'ibu_data', 'wali_data'];
    /**
     * Ambil data Ayah dari relasi wali yang sudah di-load
     */
    public function getAyahDataAttribute(): ?WaliSiswa
    {
        if (!$this->relationLoaded('wali')) return null;
        return $this->wali->first(fn($w) => $w->pivot->hubungan === 'Ayah');
    }
    /**
     * Ambil data Ibu dari relasi wali yang sudah di-load
     */
    public function getIbuDataAttribute(): ?WaliSiswa
    {
        if (!$this->relationLoaded('wali')) return null;
        return $this->wali->first(fn($w) => $w->pivot->hubungan === 'Ibu');
    }
    /**
     * Ambil data Wali Asuh dari relasi wali yang sudah di-load
     */
    public function getWaliDataAttribute(): ?WaliSiswa
    {
        if (!$this->relationLoaded('wali')) return null;
        return $this->wali->first(fn($w) => $w->pivot->hubungan === 'Wali');
    }
        /**
     * ========================================================================
     * SCOPE PENGAMAN (POLICY DATA)
     * ========================================================================
     * Mengunci akses data siswa berdasarkan jabatan Wali Kelas.
     */
    public function scopeAksesSesuaiWali($query, $user)
    {
        // 1. Jika User punya izin super (Admin), berikan akses ke SEMUA data
        if ($user->hasPermission('akses-semua-siswa')) {
            return $query; 
        }
        // 2. Jika bukan Admin, cek apakah User ini terdaftar sebagai Pegawai
        $pegawai = \App\Models\Pegawai::where('user_id', $user->id)->first();
        if ($pegawai) {
            // 3. Cek di tabel Kelas, apakah Pegawai ini menjabat sebagai Wali Kelas
            // (Pluck digunakan karena siapa tahu 1 guru memegang 2 kelas)
            $kelasIds = \App\Models\Kelas::where('wali_kelas_id', $pegawai->id)->pluck('id');
            if ($kelasIds->isNotEmpty()) {
                // 4. Jika menjabat, FILTER siswa HANYA untuk kelas yang dipegangnya
                return $query->whereIn('kelas_id', $kelasIds);
            }
        }
        // 5. Jika bukan Admin DAN bukan Wali Kelas (misal guru biasa tanpa kelas), kosongkan data!
        // Sengaja dibuat where 'id' = 0 agar query tidak error tapi hasilnya aman (kosong).
        return $query->where('id', 0);
    }
        /**
     * Relasi ke Anggota Kelompok Wali (Bimbingan)
     */
    public function anggotaKelasWali()
    {
        return $this->hasMany(AnggotaKelasWali::class, 'siswa_id', 'id');
    }
}