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
        Schema::create('peminjaman_sarpras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventaris_id')->nullable()->constrained('inventaris')->onDelete('set null');
            $table->foreignId('peminjam_id')->nullable()->constrained('pegawai')->onDelete('set null'); // Bisa pegawai atau guru
            $table->date('tanggal_pinjam');
            $table->date('tanggal_kembali_rencana');
            $table->date('tanggal_kembali_realisasi')->nullable();
            $table->enum('status', ['Dipinjam', 'Dikembalikan', 'Terlambat'])->default('Dipinjam');
            $table->text('keperluan');
            $table->text('catatan')->nullable();
            $table->foreignId('pegawai_id_pencatat')->nullable()->constrained('pegawai')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman_sarpras');
    }
};
