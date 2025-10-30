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
            // Drop deprecated kategori_masalah column (replaced by problem_category_id)
            if (Schema::hasColumn('laporan', 'kategori_masalah')) {
                $table->dropColumn('kategori_masalah');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan', function (Blueprint $table) {
            //
        });
    }
};
