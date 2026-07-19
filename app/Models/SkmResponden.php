<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkmResponden extends Model
{
    protected $table = 'skm_responden';
    protected $guarded = ['id'];

    public function layanan()
    {
        return $this->belongsTo(SkmLayanan::class, 'layanan_id');
    }

    public function jawaban()
    {
        return $this->hasMany(SkmJawaban::class, 'responden_id');
    }
}