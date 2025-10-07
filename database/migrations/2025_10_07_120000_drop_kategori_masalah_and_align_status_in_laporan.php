<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop legacy column if exists
        if (Schema::hasColumn('laporan', 'kategori_masalah')) {
            Schema::table('laporan', function (Blueprint $table) {
                $table->dropColumn('kategori_masalah');
            });
        }

        // Align status values used by app: 'In Progress' or 'Selesai'
        // If the column is enum with different set, convert to string then normalize
        // 1) Ensure column type can hold the values
        Schema::table('laporan', function (Blueprint $table) {
            $table->string('status', 32)->default('In Progress')->change();
        });

        // 2) Normalize existing data
        DB::table('laporan')->where('status', 'Ditugaskan')->update(['status' => 'In Progress']);
        DB::table('laporan')->where('status', 'Proses')->update(['status' => 'In Progress']);
    }

    public function down(): void
    {
        // Recreate legacy column as nullable string (cannot restore data)
        Schema::table('laporan', function (Blueprint $table) {
            if (!Schema::hasColumn('laporan', 'kategori_masalah')) {
                $table->string('kategori_masalah')->nullable()->after('departemen_supervisor_id');
            }
            // Revert status type to enum-ish via string with default 'Ditugaskan'
            $table->string('status', 32)->default('Ditugaskan')->change();
        });
    }
};


