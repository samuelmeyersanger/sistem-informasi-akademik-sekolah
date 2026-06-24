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
        Schema::create('riwayat_status_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            
            // Tambahan Rekomendasi: Mengetahui status ini berubah di semester & TA mana
            $table->foreignId('semester_id')->nullable()->constrained('semester')->onDelete('set null');
            
            $table->enum('status', ['Aktif', 'Lulus', 'Mutasi', 'Keluar']);
            $table->jsonb('metadata')->nullable(); // Menyimpan data dinamis alasan, sekolah tujuan, dll.
            $table->timestamps();
            $table->softDeletes();

            // Index yang ditingkatkan untuk mencakup pencarian berbasis semester akademik
            $table->index(['siswa_id', 'status', 'semester_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_status_siswa');
    }
};