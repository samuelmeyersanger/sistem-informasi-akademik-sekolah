<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Loggable;

class SettingKontak extends Model
{
    use Loggable;
    /**
     * Nama tabel yang terikat dengan model ini.
     * Wajib didefinisikan agar Laravel tidak mencari tabel bernama 'setting_kontaks'.
     *
     * @var string
     */
    protected $table = 'setting_kontak';

    /**
     * Kolom-kolom yang diizinkan untuk diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
        'deskripsi',
    ];
}