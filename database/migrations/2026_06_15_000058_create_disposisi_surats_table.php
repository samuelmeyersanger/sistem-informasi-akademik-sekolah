<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disposisi_surat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_masuk_id')->constrained('surat_masuk')->onDelete('cascade');
            $table->foreignId('dari_user_id')->constrained('users')->onDelete('cascade'); // Kepsek
            $table->foreignId('kepada_user_id')->constrained('users')->onDelete('cascade'); // Guru/Waka
            $table->text('catatan_instruksi'); 
            $table->string('sifat_disposisi'); // Sangat Segera, Segera, Biasa
            $table->enum('status', ['Belum Dibaca', 'Diproses', 'Selesai'])->default('Belum Dibaca');
            $table->timestamp('dibaca_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disposisi_surat');
    }
};