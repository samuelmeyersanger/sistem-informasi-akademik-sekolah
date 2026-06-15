<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FooterLink extends Model
{
    /**
     * Nama tabel yang terikat dengan model ini.
     *
     * @var string
     */
    protected $table = 'footer_links';

    /**
     * Kolom-kolom yang diizinkan untuk diisi secara massal (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'group',
        'judul',
        'url',
        'urutan',
        'status',
    ];

    /**
     * Casting tipe data otomatis dari Laravel.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'boolean',
        'urutan' => 'integer',
    ];

    /**
     * Scope untuk mengambil tautan yang berstatus aktif saja dan diurutkan.
     * Cara pakai di Controller/Provider: FooterLink::active()->get();
     */
    public function scopeActive($query)
    {
        return $query->where('status', true)->orderBy('urutan', 'asc');
    }
}