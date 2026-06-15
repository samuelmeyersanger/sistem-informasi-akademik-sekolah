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
        Schema::create('pengaturan_logo', function (Blueprint $table) {
            $table->id();
            $table->string('logo_pemda')->nullable();
            $table->string('logo_sekolah')->nullable();
            $table->string('kop_surat')->nullable();
            $table->string('ttd_kepala_sekolah')->nullable();
            $table->string('stempel_sekolah')->nullable();
            $table->string('ttd_dan_stempel')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan_logo');
    }
};
