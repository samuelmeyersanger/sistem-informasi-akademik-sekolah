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
        Schema::create('kode_guru_mata_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kode_guru_id')->constrained('kode_guru')->onDelete('cascade');
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajaran')->onDelete('cascade');
            $table->integer('jam_mengajar_porsi'); // Menyimpan jam masing-masing mapel saat di-attach
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kode_guru_mata_pelajaran');
    }
};
