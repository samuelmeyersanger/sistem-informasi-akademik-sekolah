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
        Schema::create('prestasi_ekstrakurikuler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ekstrakurikuler_id')->nullable()->constrained('ekstrakurikuler')->onDelete('set null');
            $table->string('nama_prestasi');
            $table->string('tingkat'); // Sekolah, Kabupaten, Provinsi, Nasional
            $table->string('juara')->nullable(); // 1, 2, 3 atau Harapan
            $table->date('tanggal_prestasi');
            $table->string('penyelenggara');
            $table->string('file_sertifikat')->nullable();
            $table->string('file_dokumentasi')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestasi_ekstrakurikuler');
    }
};
