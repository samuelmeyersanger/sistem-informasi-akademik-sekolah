<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('chat_room', function (Blueprint $table) {
            $table->id();
            $table->string('nama_room')->nullable(); // Diisi jika tipe = 'Grup'
            $table->enum('tipe', ['Pribadi', 'Grup'])->default('Pribadi');
            $table->foreignId('pembuat_id')->nullable()->constrained('pegawai')->onDelete('set null');
            $table->text('deskripsi')->nullable();
            $table->string('avatar')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
            $table->softDeletes(); // Mendukung fitur hapus grup (soft delete)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_room');
    }
};
