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
        Schema::create('tanggal_rapor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->nullable()->constrained('tahun_ajaran')->onDelete('set null');
            $table->foreignId('semester_id')->nullable()->constrained('semester')->onDelete('set null');
            $table->string('tempat_cetak');
            $table->date('tanggal_cetak');
            
            // Snapshot Data Kepala Sekolah 
            $table->string('nama_kepala_sekolah');
            $table->string('nip_kepala_sekolah')->nullable();
            $table->string('label_kepala_sekolah')->default('Kepala Sekolah');
            $table->string('label_nip_kepala_sekolah')->default('NIP.');
            $table->string('label_nip_wali_kelas')->default('NIP.');
            
            $table->softDeletes();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tanggal_rapor');
    }
};