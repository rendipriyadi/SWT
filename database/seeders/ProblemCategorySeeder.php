<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProblemCategory;

class ProblemCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Safety: Potential hazard',
                'description' => 'Safety related issues and potential hazards in the workplace',
                'color' => '#dc3545',
                'is_active' => true,
                'sort_order' => 1
            ],
            [
                'name' => '5S: Seiri, Seiton, Seiketsu, Shitsuke',
                'description' => '5S methodology including Seiri (Sort), Seiton (Set in order), Seiketsu (Standardize), and Shitsuke (Sustain)',
                'color' => '#28a745',
                'is_active' => true,
                'sort_order' => 2
            ]
        ];

        foreach ($categories as $category) {
            ProblemCategory::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}