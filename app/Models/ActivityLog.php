<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id', 
        'activity', 
        'model', 
        'properties', 
        'ip_address', 
        'user_agent'
    ];

    // Otomatis mengubah JSON di database menjadi Array PHP
    protected $casts = [
        'properties' => 'array',
    ];

    // Relasi ke User yang melakukan aksi
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}