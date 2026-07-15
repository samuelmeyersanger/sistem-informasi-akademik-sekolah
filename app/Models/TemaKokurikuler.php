<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Loggable;

class TemaKokurikuler extends Model
{
    use HasFactory, SoftDeletes, Loggable;

    /**
     * Nama tabel yang terhubung dengan model ini.
     *
     * @var string
     */
    protected $table = 'tema_kokurikuler';

    /**
     * Atribut yang diizinkan untuk diisi secara massal (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tema',
        'is_aktif',
    ];

    /**
     * Konversi tipe data atribut (Casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_aktif' => 'boolean', // Memastikan nilai is_aktif selalu true/false, bukan 1/0
    ];
}