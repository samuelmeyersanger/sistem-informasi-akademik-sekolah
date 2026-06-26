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
        Schema::create('jadwal_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', "Jumat", 'Sabtu']);
            $table->foreignId('waktu_kbm_id')->nullable()->constrained('waktu_kbm')->onDelete('set null');
            $table->foreignId('kelas_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('kode_guru_id')->nullable()->constrained('kode_guru')->onDelete('set null');
            $table->foreignId('ruangan_id')->nullable()->constrained('ruangan')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_pelajaran');
    }
};
