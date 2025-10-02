<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Area;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = [
            ['id' => 1, 'name' => 'Manufacture'],
            ['id' => 2, 'name' => 'Quality Control'],
            ['id' => 3, 'name' => 'Warehouse'],
        ];

        foreach ($areas as $area) {
            Area::updateOrCreate(['id' => $area['id']], $area);
        }

        $this->command->info('Areas seeded successfully.');
    }
}
