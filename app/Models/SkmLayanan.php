<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkmLayanan extends Model
{
    protected $table = 'skm_layanan';
    protected $guarded = ['id'];

    public function responden()
    {
        return $this->hasMany(SkmResponden::class, 'layanan_id');
    }
}