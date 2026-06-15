<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    /**
     * Kolom-kolom yang diizinkan untuk diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'content',
        'meta_description',
        'is_published',
        'sort_order',
    ];

    /**
     * Casting tipe data otomatis dari Laravel.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_published' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Booted function untuk mengotomatisasi pembuatan Slug.
     * Jadi, setiap kali Anda menyimpan halaman baru (misal judul: "Visi Misi Sekolah"),
     * sistem akan otomatis mengubahnya menjadi "visi-misi-sekolah" di kolom slug.
     */
    protected static function booted()
    {
        static::creating(function ($page) {
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->title);
            }
        });

        static::updating(function ($page) {
            if ($page->isDirty('title') && empty($page->slug)) {
                $page->slug = Str::slug($page->title);
            }
        });
    }

    /**
     * Scope untuk mempermudah mengambil halaman yang hanya berstatus 'published' (Aktif).
     * Cara pakai di Controller: $pages = Page::published()->get();
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}