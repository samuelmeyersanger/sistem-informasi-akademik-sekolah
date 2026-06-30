<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Loggable;

class SuratMasuk extends Model
{
    use Loggable;
    protected $table = 'surat_masuk';
    protected $fillable = ['nomor_surat', 'asal_instansi', 'perihal', 'tanggal_surat', 'tanggal_terima', 'file_surat', 'sifat', 'penerima_id'];

    public function penerima() { return $this->belongsTo(User::class, 'penerima_id'); }
    public function disposisi() { return $this->hasMany(DisposisiSurat::class); }
}