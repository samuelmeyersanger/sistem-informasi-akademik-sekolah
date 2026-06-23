<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Loggable;

class Permission extends Model
{
    use Loggable;
    protected $fillable = ['name', 'modul', 'description'];

    // Relasi kebalikannya: Banyak Permission dimiliki oleh banyak Role
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
    
}