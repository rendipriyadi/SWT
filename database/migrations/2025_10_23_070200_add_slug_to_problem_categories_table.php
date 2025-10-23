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
        Schema::table('problem_categories', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable()->after('name');
        });

        // Generate slugs for existing records
        $categories = \App\Models\ProblemCategory::all();
        foreach ($categories as $category) {
            $category->slug = \Illuminate\Support\Str::slug($category->name);
            $category->save();
        }

        // Make slug non-nullable after populating
        Schema::table('problem_categories', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('problem_categories', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
