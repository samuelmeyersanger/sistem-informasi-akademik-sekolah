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
        Schema::create('waktu_kbm', function (Blueprint $table) {
            $table->id();
            $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', "Jumat", 'Sabtu']);
            $table->string('jam_ke');
            $table->time('waktu_mulai');
            $table->time('waktu_selesai');
            $table->enum('kegiatan', ['Upacara', 'G7', 'Korikuler', 'MBG', 'KBM', 'Istirahat']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waktu_kbms');
    }
};
