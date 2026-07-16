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
        Schema::create('profil_lulusan', function (Blueprint $table) {
            $table->id();
            $table->string('no');
            $table->string('tema');
            $table->string('dimensi_profil_lulusan',);
            $table->string('subdmensi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profil_lulusan');
    }
};