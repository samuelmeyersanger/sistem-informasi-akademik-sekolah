<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('gaya_belajar_soal', function (Blueprint $table) {
            $table->id();
            $table->text('pertanyaan');
            
            // Opsi jawaban berdasarkan gaya belajar
            $table->string('opsi_visual');
            $table->string('opsi_auditory');
            $table->string('opsi_kinesthetic');
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gaya_belajar_soal');
    }
};