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
        Schema::create('kktp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('set null');
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('set null');
            $table->foreignId('tujuan_pembelajaran_id')->constrained('tujuan_pembelajaran')->onDelete('set null');
            $table->integer('tercapai')->nullable();
            $table->integer('tidak_tercapai')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kktp');
    }
};
