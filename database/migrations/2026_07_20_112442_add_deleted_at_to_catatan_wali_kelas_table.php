<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('catatan_wali_kelas', function (Blueprint $table) {
            // Suntikkan kolom deleted_at
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('catatan_wali_kelas', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};