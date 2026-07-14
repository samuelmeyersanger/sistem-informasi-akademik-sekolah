<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\Loggable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, Loggable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_approved', // Tambahkan ini
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_approved' => 'boolean',
        ];
    }

    /**
     * Relasi Many-to-Many custom ke model Role
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role', 'user_id', 'role_id');
    }

    /**
     * Helper untuk mengecek apakah user punya role tertentu (untuk middleware/blade jika dibutuhkan)
     */
    public function hasRole($roleName)
    {
        // Diubah ke 'name' karena model Role Anda menggunakan kolom 'name' (bukan slug)
        return $this->roles->contains('name', $roleName); 
    }

    public function hasPermission(string $permissionName): bool
    {
        if ($this->role === 'admin' || $this->role === 'super-admin') {
            return true;
        }
        $roles = $this->roles()->with('permissions')->get();
        foreach ($roles as $role) {
            // 🟢 UBAH 'name' MENJADI 'slug' (Atau sesuaikan dengan nama kolom asli di DB Anda)
            if ($role->permissions->contains('name', $permissionName)) { 
                return true;
            }
        }
        return false;
    }
}