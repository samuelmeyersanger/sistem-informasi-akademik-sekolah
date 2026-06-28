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
        Schema::create('izin_pegawai', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawai')->onDelete('set null');
            $table->foreignId('mata_pelajaran_id')->nullable()->constrained('mata_pelajaran')->onDelete('set null');
            $table->time('waktu_keluar');
            $table->time('waktu_kembali')->nullable();
            $table->text('alasan_keluar');
            $table->foreignId('invaler_id')->nullable()->constrained('pegawai')->onDelete('set null');
            $table->string('tanda_tangan_piket')->nullable();
            $table->string('tanda_tangan_pegawai')->nullable();
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
        Schema::dropIfExists('izin_pegawai');
    }
};
