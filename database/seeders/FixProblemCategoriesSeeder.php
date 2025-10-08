<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixProblemCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Fixing problem_categories duplicates...');

        // First, delete all existing problem_categories
        $deletedCount = DB::table('problem_categories')->count();
        DB::table('problem_categories')->truncate();
        $this->command->info("Deleted {$deletedCount} existing problem_categories");

        // Then insert the correct data
        $categories = [
            [
                'name' => 'Safety: Potential hazard',
                'description' => 'Safety related issues and potential hazards in the workplace',
                'color' => '#dc3545',
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => '5S: Seiri, Seiton, Seiketsu, Shitsuke',
                'description' => '5S methodology including Seiri (Sort), Seiton (Set in order), Seiketsu (Standardize), and Shitsuke (Sustain)',
                'color' => '#28a745',
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('problem_categories')->insert($categories);
        $this->command->info('Inserted 2 problem categories successfully');
    }
}
