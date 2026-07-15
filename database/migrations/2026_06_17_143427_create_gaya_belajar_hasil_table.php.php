<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('gaya_belajar_hasil', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel siswa (pastikan tipe data id di tabel siswa sesuai)
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            
            // Skor per kategori
            $table->integer('skor_visual')->default(0);
            $table->integer('skor_auditory')->default(0);
            $table->integer('skor_kinesthetic')->default(0);
            
            // Hasil Akhir (Visual / Auditory / Kinesthetic)
            $table->string('gaya_dominan')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gaya_belajar_hasil');
    }
};