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
        Schema::create('pemanggilan_orang_tua', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_panggilan');
            $table->foreignId('siswa_id')->nullable()->constrained('siswa')->onDelete('set null');
            $table->foreignId('wali_id')->nullable()->constrained('wali_siswa')->onDelete('set null');
            $table->text('alasan_panggilan');
            $table->enum('status', ['Terpanggil', 'Tidak Hadir', 'Dijadwalkan Ulang'])->default('Terpanggil');
            $table->date('tanggal_kehadiran')->nullable();
            $table->text('hasil_pertemuan')->nullable();
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawai')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemanggilan_orang_tua');
    }
};
