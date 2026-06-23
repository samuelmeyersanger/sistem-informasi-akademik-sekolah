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
        // Create class table (kelas)
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelas');
            $table->enum('tingkat', ['7', '8', '9']);
            $table->foreignId('wali_kelas_id')->nullable()->constrained('pegawai')->onDelete('set null');
            $table->foreignId('semester_id')->nullable()->constrained('semester')->onDelete('set null');
            $table->integer('jumlah_siswa')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
