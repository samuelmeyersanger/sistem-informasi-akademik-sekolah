<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Master Layanan (Contoh: PPDB, Legalisir, dll)
        Schema::create('skm_layanan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_layanan');
            $table->boolean('status')->default(true); // true = aktif
            $table->timestamps();
        });

        // 2. Tabel Master Unsur (Pertanyaan Survei)
        Schema::create('skm_unsur', function (Blueprint $table) {
            $table->id();
            $table->string('kode_unsur', 10)->unique(); // Misal: U1, U2
            $table->text('pertanyaan');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 3. Tabel Data Responden Masyarakat
        Schema::create('skm_responden', function (Blueprint $table) {
            $table->id();
            $table->foreignId('layanan_id')->constrained('skm_layanan')->cascadeOnDelete();
            
            // Demografi Opsional (Bisa diisi / anonim)
            $table->string('nama_lengkap')->nullable();
            $table->string('nomor_hp', 20)->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->integer('umur')->nullable();
            $table->enum('pendidikan_terakhir', ['SD', 'SMP', 'SMA', 'D3', 'S1', 'S2', 'S3', 'Lainnya'])->nullable();
            $table->enum('pekerjaan', ['PNS', 'TNI/Polri', 'Swasta', 'Wiraswasta', 'Pelajar/Mahasiswa', 'Lainnya'])->nullable();
            
            $table->text('saran_masukan')->nullable();
            $table->timestamps();
        });

        // 4. Tabel Detail Jawaban Responden (Skala 1 - 4)
        Schema::create('skm_jawaban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('responden_id')->constrained('skm_responden')->cascadeOnDelete();
            $table->foreignId('unsur_id')->constrained('skm_unsur')->cascadeOnDelete();
            
            // Nilai Bintang (1 = Buruk, 2 = Cukup, 3 = Baik, 4 = Sangat Baik)
            $table->tinyInteger('nilai'); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skm_jawaban');
        Schema::dropIfExists('skm_responden');
        Schema::dropIfExists('skm_unsur');
        Schema::dropIfExists('skm_layanan');
    }
};