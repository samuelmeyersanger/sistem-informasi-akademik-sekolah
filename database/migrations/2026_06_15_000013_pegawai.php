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
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('semester_id')->nullable()->constrained('semester')->onDelete('set null');
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['Laki-Laki', 'Perempuan']);
            $table->string('nip')->nullable()->unique();
            $table->string('nuptk')->nullable()->unique();
            $table->enum('status_pegawai', ['PNS', 'PPPK', 'HONORER']);
            $table->string('pangkat_golongan')->nullable();
            $table->enum('jenis_ptk', ['Kepala Sekolah', 'Guru', 'Tenaga Kependidikan']);
            $table->enum('status_keaktifan', ['Aktif', 'Mutasi', 'Pensiun'])->default('Aktif');
            $table->string('email')->nullable();
            
            // Mutasi
            $table->date('tanggal_mutasi')->nullable();
            $table->text('alasan_mutasi')->nullable();
            $table->string('sekolah_tujuan')->nullable();
            $table->string('file_surat_mutasi')->nullable();
            
            // Pensiun
            $table->date('tanggal_pensiun')->nullable();
            $table->string('file_surat_pensiun')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};
