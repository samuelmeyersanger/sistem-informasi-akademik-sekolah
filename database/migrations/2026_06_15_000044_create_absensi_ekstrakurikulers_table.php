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
        Schema::create('absensi_ekstrakurikuler', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('anggota_ekstrakurikuler_id')->nullable()->constrained('anggota_ekstrakurikuler')->onDelete('set null');
            $table->foreignId('ekstrakurikuler_id')->nullable()->constrained('ekstrakurikuler')->onDelete('set null');
            $table->enum('status', ['Hadir', 'Izin', 'Sakit', 'Alpha'])->default('Hadir');
            $table->string('tanda_tangan')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_ekstrakurikuler');
    }
};
