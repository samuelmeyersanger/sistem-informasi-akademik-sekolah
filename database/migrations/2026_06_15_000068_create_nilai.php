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
        Schema::create('nilai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('set null');
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('set null');
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajaran')->onDelete('set null');
            
            $table->json('nilai_sumatif')->nullable();
            $table->decimal('rata_sumatif', 5, 2)->nullable(); // <-- TAMBAHAN
            $table->decimal('psts', 5, 2)->nullable();
            $table->decimal('psas', 5, 2)->nullable();
            $table->decimal('nilai_rapor', 5, 2)->nullable(); // <-- TAMBAHAN
            
            $table->timestamps();
            $table->unique(['siswa_id', 'mata_pelajaran_id', 'kelas_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai');
    }
};