<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CleanupDuplicateDataSeeder::class,
            DepartemenSupervisorSeeder::class,
            AreaSeeder::class,
            PenanggungJawabSeeder::class,
            ProblemCategorySeeder::class,
            // LaporanSeeder::class, // disabled: no dummy reports/history
        ]);
    }
}
