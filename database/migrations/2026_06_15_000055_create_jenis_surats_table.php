<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_surat', function (Blueprint $table) {
            $table->id();
            $table->string('kode_klasifikasi')->unique(); // Contoh: 420, 800
            $table->string('nama_jenis'); // Contoh: Surat Tugas, Surat Undangan
            $table->string('format_nomor'); // Contoh: [NOMOR]/[KODE]/SMK-1/[BULAN]/[TAHUN]
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_surat');
    }
};