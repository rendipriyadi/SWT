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
        // 1. Hapus foreign key constraint dari departemen_supervisor_id
        Schema::table('laporan', function (Blueprint $table) {
            $table->dropForeign(['departemen_supervisor_id']);
            $table->dropColumn('departemen_supervisor_id');
        });
        
        // 2. Tidak menghapus tabel departemen_supervisors untuk sementara
        // Agar aplikasi tetap berfungsi hingga seluruh kode terupdate
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan', function (Blueprint $table) {
            $table->unsignedBigInteger('departemen_supervisor_id')->nullable()->after('Foto');
            $table->foreign('departemen_supervisor_id')->references('id')->on('departemen_supervisors');
        });
    }
};
