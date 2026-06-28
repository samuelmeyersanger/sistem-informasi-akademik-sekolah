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
        Schema::create('izin_keluar_siswa', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->onDelete('set null');
            $table->foreignId('siswa_id')->nullable()->constrained('siswa')->onDelete('set null');
            $table->time('waktu_keluar');
            $table->time('waktu_kembali')->nullable();
            $table->text('alasan_keluar');
            $table->string('tanda_tangan_piket')->nullable();
            $table->string('tanda_tangan_siswa')->nullable();
            $table->enum('status', ['Belum Kembali', 'Sudah Kembali'])->default('Belum Kembali');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('izin_keluar_siswa');
    }
};
