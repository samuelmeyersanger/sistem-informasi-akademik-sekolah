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
        Schema::create('dokumen_siswa', function (Blueprint $table) {
            $table->id();
            
            // Diubah ke cascade agar tidak meninggalkan berkas sampah saat siswa dihapus
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            
            $table->string('jenis_dokumen'); // Contoh: Akta Kelahiran, KK, Ijazah, KIP
            $table->string('nama_dokumen');  // Contoh: Akta_Kelahiran_Budi.pdf
            $table->year('tahun_dokumen');   // Tahun penerbitan dokumen
            $table->string('file_dokumen');  // Path lokasi file di storage (e.g., 'documents/siswa/xx.pdf')
            $table->timestamps();
            $table->softDeletes();
            
            // Opsional: Bagus untuk performa query pencarian dokumen per siswa
            $table->index('siswa_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_siswa');
    }
};