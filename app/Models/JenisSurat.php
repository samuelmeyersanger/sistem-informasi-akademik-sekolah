<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Loggable;

class JenisSurat extends Model
{
    use Loggable;
    protected $table = 'jenis_surat';
    protected $fillable = ['kode_klasifikasi', 'nama_jenis', 'format_nomor'];
}