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
        $tables = [
            'nilai',
            'kktp',
            'nilai_kokurikuler',
            'nilai_ekstrakurikuler',
            'kehadiran',
            'catatan_wali_kelas',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreignId('semester_id')->nullable()->constrained('semester')->onDelete('set null');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'nilai',
            'kktp',
            'nilai_kokurikuler',
            'nilai_ekstrakurikuler',
            'kehadiran',
            'catatan_wali_kelas',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'semester_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropForeign(['semester_id']);
                    $table->dropColumn('semester_id');
                });
            }
        }
    }
};
