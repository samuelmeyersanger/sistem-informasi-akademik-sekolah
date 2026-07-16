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
        Schema::create('kegiatan_kokurikuler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tema_kokurikuler_id')->nullable()->constrained('tema_kokurikuler')->onDelete('set null');
            $table->string('no_urut');
            $table->string('nama_kegiatan_kokurikuler');
            $table->string('tujuan_akhir_kegiatan');
            $table->foreignId('profil_lulusan_id')->nullable()->constrained('profil_lulusan')->onDelete('set null');
            $table->enum('tingkat', ['7', '8', '9']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kegiatan_kokurikuler');
    }
};