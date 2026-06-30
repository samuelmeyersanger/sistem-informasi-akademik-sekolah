<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Pastikan tabel surat_keluar Anda memiliki kolom header tambahan ini:
        Schema::table('surat_keluar', function (Blueprint $table) {
            $table->string('header_1')->nullable()->after('isi_surat');
            $table->string('header_2')->nullable()->after('header_1');
            $table->string('header_3')->nullable()->after('header_2');
            $table->string('header_4')->nullable()->after('header_3');
            $table->string('header_5')->nullable()->after('header_4');
        });

        // 2. Buat tabel anak untuk menyimpan baris data Excel secara universal
        Schema::create('surat_keluar_lampiran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_keluar_id')->constrained('surat_keluar')->onDelete('cascade');
            $table->string('kolom_1')->nullable();
            $table->string('kolom_2')->nullable();
            $table->string('kolom_3')->nullable();
            $table->string('kolom_4')->nullable();
            $table->string('kolom_5')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_keluar_lampiran');
        Schema::table('surat_keluar', function (Blueprint $table) {
            $table->dropColumn(['header_1', 'header_2', 'header_3', 'header_4', 'header_5']);
        });
    }
};