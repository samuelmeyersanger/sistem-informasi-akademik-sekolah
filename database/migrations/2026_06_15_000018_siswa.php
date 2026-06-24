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
        // Create student table (siswa)
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('semester_id')->nullable()->constrained('semester')->onDelete('set null');
            $table->string('nama_lengkap');
            $table->string('nipd')->unique();
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->string('nisn')->nullable();
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('nik')->unique();
            $table->enum('agama', ['Islam', 'Kristen', 'Katholik', 'Hindu', 'Budha']);
            $table->text('alamat_lengkap');
            $table->string('rt');
            $table->string('rw');
            $table->string('provinsi');
            $table->string('kota');
            $table->string('kelurahan_desa');
            $table->string('kecamatan');
            $table->string('kode_pos');
            $table->string('nomor_hp');
            $table->string('no_peserta_un')->nullable();
            $table->string('asal_sekolah');
            $table->integer('anak_ke');
            $table->enum('tingkat', ['7', '8', '9']);
            $table->date('diterima_pada_tanggal');
            $table->enum('status_siswa', ['Aktif', 'Lulus', 'Keluar', 'Mutasi'])->default('Aktif');
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};
