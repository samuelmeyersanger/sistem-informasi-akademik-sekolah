<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

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

    public function roles()
    {
        // Relasi Many-to-Many custom ke model Role milikmu
        return $this->belongsToMany(Role::class, 'user_role', 'user_id', 'role_id');
    }

    /**
     * Helper untuk mengecek apakah user punya role tertentu (untuk middleware/blade)
     */
    public function hasRole($roleName)
    {
        return $this->roles->contains('slug', $roleName); // Sesuaikan 'slug' atau 'name' dengan kolom di tabel roles-mu
    }

    /**
     * Fungsi kustom untuk mengecek apakah user memiliki hak akses tertentu
     * Contoh pemakaian: auth()->user()->hasPermission('erapor.input')
     */
    public function hasPermission(string $permissionName): bool
    {
        // Jika user tidak punya role, otomatis tidak punya akses
        if (! $this->role) {
            return false;
        }

        // Ambil semua permission yang dimiliki oleh role user tersebut, lalu cek apakah ada yang cocok
        return $this->role->permissions->contains('name', $permissionName);
    }
}
