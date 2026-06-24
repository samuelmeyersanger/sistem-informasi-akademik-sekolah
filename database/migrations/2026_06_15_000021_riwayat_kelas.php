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
        Schema::create('riwayat_kelas_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->onDelete('set null');
            $table->enum('tingkat', ['7', '8', '9']); // Tambahan agar tahu di tingkat mana saat itu
            $table->foreignId('semester_id')->nullable()->constrained('semester')->onDelete('set null');
            $table->string('keterangan')->nullable(); // Tambahan: Contoh isi: "Naik Kelas", "Tinggal Kelas", "Siswa Pindahan", "Lulus"
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_kelas_siswa');
    }
};
