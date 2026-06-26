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
        Schema::create('inventaris', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang')->unique();
            $table->string('nama_barang');
            $table->text('deskripsi')->nullable();
            $table->string('kategori')->nullable(); // Mebel, Elektronik, dll
            $table->string('merek')->nullable();
            $table->string('model')->nullable();
            $table->year('tahun_pembelian')->nullable();
            $table->decimal('harga_perolehan', 12, 2)->nullable();
            $table->enum('kondisi', ['Baik', 'Rusak Ringan', 'Rusak Berat', 'Hilang'])->default('Baik');
            $table->string('lokasi')->nullable();
            $table->foreignId('ruangan_id')->nullable()->constrained('ruangan')->onDelete('set null');
            $table->foreignId('gedung_id')->nullable()->constrained('gedung')->onDelete('set null');
            $table->integer('jumlah')->default(1);
            $table->string('foto_barang')->nullable();
            $table->date('tanggal_penghapusan')->nullable();
            $table->text('alasan_penghapusan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventaris');
    }
};
