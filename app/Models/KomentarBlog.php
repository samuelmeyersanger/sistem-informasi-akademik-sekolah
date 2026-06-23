<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Blog;
use App\Traits\Loggable;

class KomentarBlog extends Model
{
    
    use Loggable;

    /**
     * Nama tabel yang terikat dengan model ini.
     * Wajib didefinisikan agar Laravel tidak mencari tabel bernama 'komentar_blogs'.
     *
     * @var string
     */
    protected $table = 'komentar_blog';

    /**
     * Kolom-kolom yang diizinkan untuk diisi secara massal (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'blog_id',
        'nama',
        'email',
        'komentar',
        'disetujui',
    ];

    /**
     * Casting tipe data otomatis dari Laravel.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'disetujui' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope untuk mempermudah mengambil komentar yang sudah disetujui (lolos moderasi).
     * Cara pakai di Controller: KomentarBlog::approved()->get();
     */
    public function scopeApproved($query)
    {
        return $query->where('disetujui', true)->orderBy('created_at', 'asc');
    }

    /**
     * Scope untuk mempermudah admin melihat komentar yang baru masuk dan butuh persetujuan.
     */
    public function scopePending($query)
    {
        return $query->where('disetujui', false)->orderBy('created_at', 'desc');
    }

    /* -------------------------------------------------------------------------- */
    /* RELASI ELOQUENT                                                            */
    /* -------------------------------------------------------------------------- */

    /**
     * Relasi balik ke model Blog
     * Banyak komentar terikat pada satu artikel Blog (Many to One)
     */
    public function blog()
    {
        return $this->belongsTo(Blog::class, 'blog_id');
    }
}