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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            
            $table->string('kategori')->nullable();
            
            // Nama menu yang tampil di sidebar (Contoh: "Data Siswa", "Logo Sekolah")
            $table->string('nama_menu');
            
            // Jalur URL rute tujuan (Contoh: "admin/dashboard", "admin/pengaturan-logo")
            $table->string('url');
            
            // Emoji atau class icon (Contoh: "📊", "🖼️", "👨‍🎓")
            $table->string('icon')->nullable();
            
            // Kolom angka untuk menyusun posisi menu (Contoh: 1, 2, 3, dst)
            $table->integer('urutan')->default(0);
            
            // Kunci otomatisasi: Menghubungkan ke slug permission buatan Anda sendiri
            // Dibuat nullable agar menu umum (seperti Dashboard) bisa diakses tanpa syarat permission
            $table->string('permission_slug')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};