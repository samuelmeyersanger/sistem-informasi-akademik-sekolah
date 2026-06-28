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
        Schema::create('anggota_ekstrakurikuler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ekstrakurikuler_id')->nullable()->constrained('ekstrakurikuler')->onDelete('set null');
            $table->foreignId('siswa_id')->nullable()->constrained('siswa')->onDelete('set null');
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->onDelete('set null');
            $table->string('nomor_hp')->nullable();
            $table->text('motivasi')->nullable();
            $table->date('tanggal_bergabung');
            $table->enum('status', ['Aktif', 'Tidak Aktif'])->default('Aktif');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['ekstrakurikuler_id', 'siswa_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anggota_ekstrakurikuler');
    }
};
