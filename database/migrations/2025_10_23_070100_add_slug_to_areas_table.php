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
        Schema::table('areas', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable()->after('name');
        });

        // Generate slugs for existing records
        $areas = \App\Models\Area::all();
        foreach ($areas as $area) {
            $area->slug = \Illuminate\Support\Str::slug($area->name);
            $area->save();
        }

        // Make slug non-nullable after populating
        Schema::table('areas', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('areas', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
