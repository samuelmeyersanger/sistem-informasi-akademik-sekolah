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
        Schema::create('anggota_kelas_wali', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_wali_id')->constrained('kelas_wali')->onDelete('cascade');
            $table->enum('tingkat', ['7', '8', '9']);
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('semester_id')->nullable()->constrained('semester')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anggota_kelas_wali');
    }
};