<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('chat_room_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_room_id')->nullable()->constrained('chat_room')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            
            // 🔄 UBAH BAGIAN INI: Menghubungkan langsung ke tabel roles custom Anda secara dinamis
            $table->foreignId('role_id')->nullable()->constrained('roles')->onDelete('set null');
            
            $table->timestamp('last_read_at')->nullable();
            $table->boolean('is_muted')->default(false);
            $table->boolean('is_left')->default(false);
            $table->timestamp('left_at')->nullable();
            $table->timestamps();

            $table->unique(['chat_room_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_room_members');
    }
};