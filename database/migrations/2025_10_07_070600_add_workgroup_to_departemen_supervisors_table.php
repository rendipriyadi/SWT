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
        Schema::table('departemen_supervisors', function (Blueprint $table) {
            if (!Schema::hasColumn('departemen_supervisors', 'workgroup')) {
                $table->string('workgroup')->nullable()->after('supervisor');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departemen_supervisors', function (Blueprint $table) {
            if (Schema::hasColumn('departemen_supervisors', 'workgroup')) {
                $table->dropColumn('workgroup');
            }
        });
    }
};
