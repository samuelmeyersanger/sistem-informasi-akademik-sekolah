<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\Loggable;

class KategoriBlog extends Model
{
    use Loggable;
    /**
     * Nama tabel yang terikat dengan model ini.
     * Wajib didefinisikan agar Laravel tidak mencari tabel bernama 'kategori_blogs'.
     *
     * @var string
     */
    protected $table = 'kategori_blog';

    /**
     * Kolom-kolom yang diizinkan untuk diisi secara massal (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'slug',
    ];

    /**
     * Booted function untuk mengotomatisasi pembuatan Slug dari kolom Nama.
     * Contoh: Input nama "Kegiatan Siswa" otomatis disimpan menjadi "kegiatan-siswa" di kolom slug.
     */
    protected static function booted()
    {
        static::creating(function ($kategori) {
            if (empty($kategori->slug)) {
                $kategori->slug = Str::slug($kategori->nama);
            }
        });

        static::updating(function ($kategori) {
            if ($kategori->isDirty('nama') && empty($kategori->slug)) {
                $kategori->slug = Str::slug($kategori->nama);
            }
        });
    }

    /**
     * Relasi ke model Blog
     * Satu kategori bisa memiliki banyak artikel (One to Many)
     */
    public function blogs()
    {
        return $this->hasMany(Blog::class, 'kategori_blog_id');
    }
    
}