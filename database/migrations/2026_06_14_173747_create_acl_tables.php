<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Roles (Menyimpan nama role/peran)
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();         // Contoh: 'admin', 'guru', 'siswa'
            $table->string('display_name');          // Contoh: 'Administrator', 'Guru Mata Pelajaran'
            $table->timestamps();
        });

        // 2. Tabel Permissions (Menyimpan hak akses spesifik per modul)
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();         // Contoh: 'erapor.input', 'blog.create'
            $table->string('modul');                  // Contoh: 'erapor', 'blog', 'antrian'
            $table->string('description')->nullable(); // Penjelasan fungsi hak akses
            $table->timestamps();
        });

        // 3. Tabel Pivot permission_role (Penghubung Banyak-ke-Banyak antara Role dan Permission)
        Schema::create('permission_role', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
            $table->primary(['role_id', 'permission_id']); // Mencegah duplikasi data yang sama
        });

        // 4. Modifikasi tabel users bawaan Laravel agar terhubung ke tabel roles
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('id')->constrained()->onDelete('set null');
        });
    }

    public function down(): void
    {
        // Drop foreign key dulu dari tabel users sebelum menghapus tabel utamanya
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });

        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};