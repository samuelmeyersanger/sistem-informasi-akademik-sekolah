<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkmUnsur extends Model
{
    protected $table = 'skm_unsur';
    protected $guarded = ['id'];

    public function jawaban()
    {
        return $this->hasMany(SkmJawaban::class, 'unsur_id');
    }
}