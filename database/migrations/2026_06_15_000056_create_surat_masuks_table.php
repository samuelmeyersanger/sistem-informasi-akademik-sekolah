<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_masuk', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat'); 
            $table->string('asal_instansi'); 
            $table->string('perihal');
            $table->date('tanggal_surat'); 
            $table->date('tanggal_terima'); 
            $table->string('file_surat'); // Tempat menyimpan PDF berkas scan
            $table->enum('sifat', ['Biasa', 'Penting', 'Rahasia'])->default('Biasa');
            $table->foreignId('penerima_id')->constrained('users')->onDelete('cascade'); // TU yang input
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_masuk');
    }
};