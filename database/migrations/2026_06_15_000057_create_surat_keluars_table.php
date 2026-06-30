<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_keluar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_surat_id')->constrained('jenis_surat')->onDelete('cascade');
            $table->string('nomor_surat')->nullable()->unique(); // Diisi otomatis setelah disetujui Kepsek
            $table->integer('no_urut')->nullable(); // Counter otomatis untuk nomor surat
            $table->string('tujuan_surat'); 
            $table->string('perihal');
            $table->text('isi_surat'); // Isi draf surat (bisa text biasa / rich text editor)
            $table->date('tanggal_surat');
            
            // Konfigurasi TTD ala Srikandi
            $table->enum('metode_ttd', ['Digital', 'Basah'])->default('Basah');
            $table->foreignId('penandatangan_id')->constrained('users')->onDelete('cascade'); // Target Kepsek/Waka
            $table->enum('status', ['Draf', 'Menunggu Persetujuan', 'Disetujui', 'Ditolak'])->default('Draf');
            
            $table->string('file_final')->nullable(); // Path PDF final siap cetak/kirim
            $table->foreignId('pembuat_id')->constrained('users')->onDelete('cascade'); // TU pembuat draf
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_keluar');
    }
};