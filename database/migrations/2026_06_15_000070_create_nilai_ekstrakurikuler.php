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
        Schema::create('nilai_ekstrakurikuler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ekstrakurikuler_id')->nullable()->constrained('ekstrakurikuler')->onDelete('set null');
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->enum('predikat', ['Sangat Baik', 'Baik', 'Cukup', 'Kurang']);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_ekstrakurikuler');
    }
};