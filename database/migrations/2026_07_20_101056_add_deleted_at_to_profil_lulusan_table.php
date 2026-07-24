<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('profil_lulusan', function (Blueprint $table) {
            // Ini akan menambahkan kolom deleted_at dengan aman
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profil_lulusan', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};