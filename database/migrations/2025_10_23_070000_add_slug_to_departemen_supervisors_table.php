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
            $table->string('slug')->unique()->nullable()->after('email');
        });

        // Generate slugs for existing records
        $departments = \App\Models\DepartemenSupervisor::all();
        foreach ($departments as $department) {
            $department->slug = \Illuminate\Support\Str::slug($department->supervisor . '-' . $department->departemen);
            $department->save();
        }

        // Make slug non-nullable after populating
        Schema::table('departemen_supervisors', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departemen_supervisors', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
