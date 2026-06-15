<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name', 'display_name'];

    // Relasi: Satu Role memiliki banyak Permission (Many-to-Many)
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    // Relasi: Satu Role bisa dimiliki oleh banyak User (One-to-Many)
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_role', 'role_id', 'user_id');
    }
}