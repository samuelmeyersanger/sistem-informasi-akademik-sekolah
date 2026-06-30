<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Loggable;

class SuratKeluarLampiran extends Model
{
    use Loggable;
    protected $table = 'surat_keluar_lampiran';
    protected $fillable = ['surat_keluar_id', 'kolom_1', 'kolom_2', 'kolom_3', 'kolom_4', 'kolom_5'];

    public function suratKeluar()
    {
        return $this->belongsTo(SuratKeluar::class, 'surat_keluar_id');
    }
}