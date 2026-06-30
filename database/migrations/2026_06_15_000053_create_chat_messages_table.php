<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_room_id')->nullable()->constrained('chat_room')->onDelete('cascade');
            $table->foreignId('pengirim_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('pesan');
            $table->enum('tipe_pesan', ['Teks', 'Gambar', 'File', 'Video', 'Audio'])->default('Teks');
            $table->string('file_attachment')->nullable();
            $table->string('nama_file')->nullable();
            $table->integer('ukuran_file')->nullable(); // Dalam bytes
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            
            // Fitur edit & reply pesan
            $table->foreignId('edited_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('edited_at')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->foreignId('reply_to_id')->nullable()->constrained('chat_messages')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};