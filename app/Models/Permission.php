<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['name', 'modul', 'description'];

    // Relasi kebalikannya: Banyak Permission dimiliki oleh banyak Role
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
    
}