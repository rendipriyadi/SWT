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
        Schema::table('laporan', function (Blueprint $table) {
            // Tambah kolom baru untuk relasi ke struktur baru
            // Izinkan null sementara untuk migrasi data
            $table->foreignId('area_id')->nullable()->after('departemen_supervisor_id');
            $table->foreignId('penanggung_jawab_id')->nullable()->after('area_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan', function (Blueprint $table) {
            $table->dropForeign(['penanggung_jawab_id']);
            $table->dropForeign(['area_id']);
            $table->dropColumn(['penanggung_jawab_id', 'area_id']);
        });
    }
};
