<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CleanupDuplicateDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Cleaning up duplicate data...');

        // Clean up duplicate problem_categories
        $this->cleanupProblemCategories();

        $this->command->info('Duplicate data cleanup completed.');
    }

    private function cleanupProblemCategories()
    {
        $this->command->info('Cleaning up duplicate problem_categories...');
        
        // Get all problem categories
        $categories = DB::table('problem_categories')->get();
        
        // Group by name to find duplicates
        $grouped = $categories->groupBy('name');
        
        $totalDeleted = 0;
        foreach ($grouped as $name => $duplicates) {
            if ($duplicates->count() > 1) {
                $this->command->info("Found {$duplicates->count()} duplicates for category: {$name}");
                
                // Keep the first one (lowest ID), delete the rest
                $keep = $duplicates->sortBy('id')->first();
                $toDelete = $duplicates->where('id', '>', $keep->id);
                
                foreach ($toDelete as $duplicate) {
                    DB::table('problem_categories')->where('id', $duplicate->id)->delete();
                    $this->command->info("Deleted duplicate category ID: {$duplicate->id} (name: {$duplicate->name})");
                    $totalDeleted++;
                }
            }
        }
        
        $this->command->info("Total deleted duplicates: {$totalDeleted}");
    }
}
