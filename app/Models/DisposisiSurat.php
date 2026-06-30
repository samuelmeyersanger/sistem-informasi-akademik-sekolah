<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Loggable;

class DisposisiSurat extends Model
{
    use Loggable;
    protected $table = 'disposisi_surat';
    protected $fillable = ['surat_masuk_id', 'dari_user_id', 'kepada_user_id', 'catatan_instruksi', 'sifat_disposisi', 'status', 'dibaca_at'];

    public function suratMasuk() { return $this->belongsTo(SuratMasuk::class, 'surat_masuk_id'); }
    public function pengirim() { return $this->belongsTo(User::class, 'dari_user_id'); }
    public function penerimaTugas() { return $this->belongsTo(User::class, 'kepada_user_id'); }
}