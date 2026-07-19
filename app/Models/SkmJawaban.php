<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkmJawaban extends Model
{
    protected $table = 'skm_jawaban';
    protected $guarded = ['id'];

    public function responden()
    {
        return $this->belongsTo(SkmResponden::class, 'responden_id');
    }

    public function unsur()
    {
        return $this->belongsTo(SkmUnsur::class, 'unsur_id');
    }
}