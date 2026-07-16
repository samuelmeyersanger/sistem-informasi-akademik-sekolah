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
        Schema::create('nilai_korikuler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('kegiatan_kokurikuler_id')->constrained('kegiatan_kokurikuler')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('profil_lulusan_id')->nullable()->constrained('profil_lulusan')->onDelete('set null');
            $table->foreignId('kelas_id')->constrained('kelas')->onUpdate('cascade')->onDelete('cascade');
            $table->enum('predikat', ['Berkembang', 'Cakap', 'Mahir']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_korikuler');
    }
};