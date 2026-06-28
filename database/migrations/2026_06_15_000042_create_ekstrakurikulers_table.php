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
        Schema::create('ekstrakurikuler', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->foreignId('pembina_id')->nullable()->constrained('pegawai')->onDelete('set null');
            $table->string('logo')->nullable();
            $table->enum('hari_latihan', ['Senin', 'Selasa', 'Rabu', 'Kamis', "Jumat", 'Sabtu']);
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->text('deskripsi')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ekstrakurikuler');
    }
};
