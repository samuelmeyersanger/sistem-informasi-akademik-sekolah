<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Loggable;

class SuratKeluar extends Model
{
    use Loggable;
    protected $table = 'surat_keluar';
    protected $fillable = ['jenis_surat_id', 'nomor_surat', 'no_urut', 'tujuan_surat', 'perihal', 'isi_surat', 'tanggal_surat', 'metode_ttd', 'penandatangan_id', 'status', 'file_final', 'pembuat_id'];

    public function jenisSurat() { return $this->belongsTo(JenisSurat::class, 'jenis_surat_id'); }
    public function penandatangan() { return $this->belongsTo(User::class, 'penandatangan_id'); }
    public function pembuat() { return $this->belongsTo(User::class, 'pembuat_id'); }
}