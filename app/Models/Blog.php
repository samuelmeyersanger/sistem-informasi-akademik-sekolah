<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\KategoriBlog;

class Blog extends Model
{
    /**
     * Nama tabel yang terikat dengan model ini.
     * Wajib didefinisikan agar Laravel tidak mencari tabel bernama 'blogs'.
     *
     * @var string
     */
    protected $table = 'blog';

    /**
     * Kolom-kolom yang diizinkan untuk diisi secara massal (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'kategori_blog_id',
        'judul',
        'slug',
        'konten',
        'gambar',
        'is_published',
    ];

    /**
     * Casting tipe data otomatis dari Laravel.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_published' => 'boolean',
    ];

    /**
     * Booted function untuk mengotomatisasi pembuatan Slug dari Judul Artikel.
     * Contoh: Judul "Juara 1 Lomba Debat Nasional" otomatis disimpan menjadi "juara-1-lomba-debat-nasional".
     */
    protected static function booted()
    {
        static::creating(function ($blog) {
            if (empty($blog->slug)) {
                $blog->slug = Str::slug($blog->judul);
            }
        });

        static::updating(function ($blog) {
            if ($blog->isDirty('judul') && empty($blog->slug)) {
                $blog->slug = Str::slug($blog->judul);
            }
        });
    }

    /**
     * Scope untuk mempermudah mengambil artikel yang berstatus tayang/published saja.
     * Cara pakai di Controller: Blog::published()->get();
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)->orderBy('created_at', 'desc');
    }

    /* -------------------------------------------------------------------------- */
    /* RELASI ELOQUENT                              */
    /* -------------------------------------------------------------------------- */

    /**
     * Relasi ke model User (Penulis Artikel)
     * Banyak artikel ditulis oleh satu User (Many to One)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke model KategoriBlog
     * Banyak artikel memiliki satu Kategori (Many to One)
     */
    public function kategori()
    {
        return $this->belongsTo(KategoriBlog::class, 'kategori_blog_id');
    }

    /**
     * Relasi ke model KomentarBlog
     * Satu artikel blog bisa memiliki banyak komentar (One to Many)
     */
    public function komentar()
    {
        return $this->hasMany(KomentarBlog::class, 'blog_id');
    }
    
}