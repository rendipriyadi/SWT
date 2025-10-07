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
        Schema::table('penanggung_jawab', function (Blueprint $table) {
            if (!Schema::hasColumn('penanggung_jawab', 'email')) {
                $table->string('email')->nullable()->after('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penanggung_jawab', function (Blueprint $table) {
            if (Schema::hasColumn('penanggung_jawab', 'email')) {
                $table->dropColumn('email');
            }
        });
    }
};
