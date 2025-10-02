<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PenanggungJawab;

class PenanggungJawabSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $penanggungJawabData = [
            // Area 1 (Manufacture)
            ['id' => 1, 'area_id' => 1, 'station' => 'LV Assembly', 'name' => 'Aris Setiawan'],
            ['id' => 2, 'area_id' => 1, 'station' => 'LV Box', 'name' => 'Rachmad Haryono'],
            ['id' => 3, 'area_id' => 1, 'station' => 'LV Module', 'name' => 'Hadi Djohansyah'],
            ['id' => 4, 'area_id' => 1, 'station' => 'MV Assembly', 'name' => 'Helmy Sundani'],
            ['id' => 5, 'area_id' => 1, 'station' => 'Prefabrication', 'name' => 'Sarifudin Raysan'],
            ['id' => 6, 'area_id' => 1, 'station' => 'Packing', 'name' => 'Bayu Putra Trianto'],
            ['id' => 7, 'area_id' => 1, 'station' => 'Tool Store', 'name' => 'Joni Rahman'],
            ['id' => 8, 'area_id' => 1, 'station' => 'General', 'name' => 'Tri Widardi'],
            ['id' => 9, 'area_id' => 1, 'station' => 'General', 'name' => 'Asept Surachaman'],
            
            // Area 2 (Quality Control)
            ['id' => 10, 'area_id' => 2, 'station' => 'QC LV', 'name' => 'Ishak Marthen'],
            ['id' => 11, 'area_id' => 2, 'station' => 'QC MV', 'name' => 'Sirad Nova Mihardi'],
            ['id' => 12, 'area_id' => 2, 'station' => 'IQC', 'name' => 'Abduh Al Agani'],
            ['id' => 13, 'area_id' => 2, 'station' => 'General', 'name' => 'Arif Hadi Rizali'],
            
            // Area 3 (Warehouse)
            ['id' => 14, 'area_id' => 3, 'station' => 'Warehouse', 'name' => 'Suhendra'],
            ['id' => 15, 'area_id' => 3, 'station' => 'Warehouse', 'name' => 'Wahyu Wahyudin'],
        ];

        foreach ($penanggungJawabData as $data) {
            PenanggungJawab::updateOrCreate(['id' => $data['id']], $data);
        }

        $this->command->info('Penanggung Jawab seeded successfully.');
    }
}
