<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pegawai extends Model
{
    use SoftDeletes;

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

    // Berelasi ke User (jika pegawai punya akun login)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Berelasi ke Semester
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    // Punya banyak Dokumen
    public function dokumen(): HasMany
    {
        return $this->hasMany(DokumenPegawai::class, 'pegawai_id');
    }

    // Punya banyak Riwayat Kenaikan Gaji Berkala (KGB)
    public function kgb(): HasMany
    {
        return $this->hasMany(KenaikanGajiBerkala::class, 'pegawai_id');
    }

    // Punya banyak Riwayat Kenaikan Pangkat
    public function kenaikanPangkat(): HasMany
    {
        return $this->hasMany(KenaikanPangkat::class, 'pegawai_id');
    }

    public function ekskulBinaan() 
    {
        return $this->hasMany(Ekstrakurikuler::class, 'pembina_id');
    }

        /**
     * ========================================================================
     * SCOPE PENGAMAN DATA PEGAWAI (PROFIL PRIBADI)
     * ========================================================================
     */
    public function scopeAksesPribadi($query, $user)
    {
        // 1. Jika punya hak istimewa (Admin / HRD / Kepala Sekolah)
        // (Pastikan membuat permission ini nanti!)
        if ($user->hasPermission('akses-semua-pegawai')) {
            return $query;
        }
        // 2. Jika bukan admin, KUNCI data agar hanya menunjuk ke dirinya sendiri!
        return $query->where('user_id', $user->id);
    }
    public function jadwalPelajaran()
    {
        // Ganti 'JadwalPelajaran' dengan nama Model jadwal Anda yang sebenarnya
        return $this->hasMany(JadwalPelajaran::class, 'pegawai_id'); 
    }

        /**
     * ========================================================================
     * FUNGSI BANTUAN (HELPER) BEBAN MENGAJAR
     * ========================================================================
     * Mengambil seluruh ID Mata Pelajaran yang diampu oleh Pegawai (Guru) ini.
     * Secara cerdas akan menggabungkan data dari arsitektur Many-to-Many.
     */
    public function getMapelIdsDiampu(): array
    {
        $kodeGurus = \App\Models\KodeGuru::with('mataPelajarans')
                            ->where('pegawai_id', $this->id)
                            ->get();
        
        $mapelIds = [];
        
        foreach ($kodeGurus as $kg) {
            // Ambil dari tabel pivot (Many to Many)
            foreach ($kg->mataPelajarans as $mapel) {
                $mapelIds[] = $mapel->id;
            }
            
            // Ambil dari kolom lama (jika ada)
            if ($kg->mata_pelajaran_id) {
                $mapelIds[] = $kg->mata_pelajaran_id;
            }
        }
        
        return array_unique($mapelIds);
    }
    /**
     * Mengambil seluruh ID Kelas yang diajar oleh Pegawai (Guru) ini berdasarkan Jadwal.
     */
    public function getKelasIdsDiampu(): array
    {
        $kodeGuruIds = \App\Models\KodeGuru::where('pegawai_id', $this->id)->pluck('id');
        
        // Ingat: JadwalPelajaran terhubung lewat kode_guru_id, bukan pegawai_id
        return \App\Models\JadwalPelajaran::whereIn('kode_guru_id', $kodeGuruIds)
                            ->pluck('kelas_id')
                            ->unique()
                            ->toArray();
    }
}