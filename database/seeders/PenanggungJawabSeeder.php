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
            ['id' => 1, 'area_id' => 1, 'station' => 'LV Assembly', 'name' => 'Aris Setiawan', 'email' => 'aris.setiawan@siemens.com'],
            ['id' => 2, 'area_id' => 1, 'station' => 'LV Box', 'name' => 'Rachmad Haryono', 'email' => 'rachmad.haryono@siemens.com'],
            ['id' => 3, 'area_id' => 1, 'station' => 'LV Module', 'name' => 'Hadi Djohansyah', 'email' => 'hadi.djohansyah@siemens.com'],
            ['id' => 4, 'area_id' => 1, 'station' => 'MV Assembly', 'name' => 'Helmy Sundani', 'email' => 'helmy.sundani@siemens.com'],
            ['id' => 5, 'area_id' => 1, 'station' => 'Prefabrication', 'name' => 'Sarifudin Raysan', 'email' => 'sarifudin.raysan@siemens.com'],
            ['id' => 6, 'area_id' => 1, 'station' => 'Packing', 'name' => 'Bayu Putra Trianto', 'email' => 'bayu.triyanto@siemens.com'],
            ['id' => 7, 'area_id' => 1, 'station' => 'Tool Store', 'name' => 'Joni Rahman', 'email' => 'joni.rahman@siemens.com'],
            ['id' => 8, 'area_id' => 1, 'station' => 'General', 'name' => 'Tri Widardi', 'email' => 'tri.widardi@siemens.com'],
            ['id' => 9, 'area_id' => 1, 'station' => 'General', 'name' => 'Asept Surachman', 'email' => 'asept.surachman@siemens.com'],

            // Area 2 (Quality Control)
            ['id' => 10, 'area_id' => 2, 'station' => 'QC LV', 'name' => 'Ishak Marthen', 'email' => 'ishak.ms@siemens.com'],
            ['id' => 11, 'area_id' => 2, 'station' => 'QC MV', 'name' => 'Sirad Nova Mihardi', 'email' => 'sirad.mihardi@siemens.com'],
            ['id' => 12, 'area_id' => 2, 'station' => 'IQC', 'name' => 'Abduh Al Afgani', 'email' => 'abduh.afgani@siemens.com'],
            ['id' => 13, 'area_id' => 2, 'station' => 'General', 'name' => 'Arif Hadi Rizali', 'email' => 'arif.hadi@siemens.com'],

            // Area 3 (Warehouse)
            ['id' => 14, 'area_id' => 3, 'station' => 'General', 'name' => 'Suhendra', 'email' => 'Suhendra@siemens.com'],
            ['id' => 15, 'area_id' => 3, 'station' => 'Warehouse', 'name' => 'Wahyu Wahidin', 'email' => 'wahyu.wahidin@siemens.com'],
        ];

        foreach ($penanggungJawabData as $data) {
            PenanggungJawab::updateOrCreate(['id' => $data['id']], $data);
        }

        $this->command->info('Penanggung Jawab seeded successfully.');
    }
}
