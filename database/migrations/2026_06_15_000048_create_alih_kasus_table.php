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
        Schema::create('alih_kasus', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel lain
            $table->foreignId('siswa_id')->nullable()->constrained('siswa')->onDelete('set null');
            
            // Atribut Dokumen Layanan/Kegiatan
            $table->string('topik_permasalahan');
            $table->string('bidang_bimbingan');
            $table->string('jenis_kegiatan')->default('Alih Tangan Kasus');
            $table->string('fungsi_kegiatan')->nullable();
            $table->text('tujuan_kegiatan')->nullable();
            $table->text('hasil_yang_dicapai')->nullable();
            
            // Inti Permasalahan & Alasan
            $table->text('gambaran_ringkas_masalah');
            $table->text('alasan_alih_kasus');
            
            // Detail Pihak Penerima (Pengalihan)
            $table->enum('jenis_alih', ['Ke Orang Tua', 'Ke Kepala Sekolah', 'Ke Instansi Lain', 'Ke Ahli Lain']);
            $table->string('kepada_siapa')->comment('Nama spesifik orang/instansi/ahli penerima');
            
            // Waktu Pelaksanaan
            $table->date('tanggal_alih');
            
            // Kelengkapan & Tindak Lanjut
            $table->text('bahan_disertakan')->nullable();
            $table->text('keterkaitan_layanan_terdahulu')->nullable();
            $table->text('rencana_penilaian_tindak_lanjut')->nullable();
            $table->text('catatan')->nullable(); // Catatan Khusus
            
            // Standard Laravel Timestamps & SoftDeletes
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alih_kasus');
    }
};
