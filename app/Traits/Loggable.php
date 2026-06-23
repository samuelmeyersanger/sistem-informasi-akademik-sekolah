<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Loggable
{
    /**
     * Fungsi custom untuk mencatat log manual dari Controller
     */
    public static function logActivity($activity, $properties = null, $modelName = null)
    {
        ActivityLog::create([
            'user_id'    => Auth::id(),
            'activity'   => $activity,
            'model'      => $modelName,
            'properties' => $properties,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Otomatisasi Log saat Model menggunakan Trait ini (Boot Trait)
     */
    protected static function bootLoggable()
    {
        // Mencatat otomatis saat data baru dibuat
        static::created(function ($model) {
            self::logActivity("Membuat data baru pada " . class_basename($model), $model->getAttributes(), get_class($model));
        });

        // Mencatat otomatis saat data diubah
        static::updated(function ($model) {
            $oldData = array_intersect_key($model->getOriginal(), $model->getDirty());
            $newData = $model->getDirty();

            self::logActivity("Mengubah data pada " . class_basename($model), [
                'sebelum' => $oldData,
                'sesudah' => $newData
            ], get_class($model));
        });

        // Mencatat otomatis saat data dihapus
        static::deleted(function ($model) {
            self::logActivity("Menghapus data pada " . class_basename($model), $model->getAttributes(), get_class($model));
        });
    }
}