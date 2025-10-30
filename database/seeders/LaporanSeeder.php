<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Laporan;
use App\Models\Penyelesaian;
use App\Models\Area;
use App\Models\PenanggungJawab;
use App\Models\ProblemCategory;
use Carbon\Carbon;
use Faker\Factory as Faker;

class LaporanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        
        $areaIds = Area::pluck('id')->toArray();
        $penanggungJawabIds = PenanggungJawab::pluck('id')->toArray();
        $problemCategoryIds = ProblemCategory::pluck('id')->toArray();

        if (empty($areaIds) || empty($penanggungJawabIds) || empty($problemCategoryIds)) {
            $this->command->error('Please run AreaSeeder, PenanggungJawabSeeder, and ProblemCategorySeeder first!');
            return;
        }

        $this->command->info("Generating sample laporan with " . count($problemCategoryIds) . " problem categories...");

        // Generate 60 laporan Assigned (in progress)
        for ($i = 0; $i < 60; $i++) {
            $areaId = $faker->randomElement($areaIds);
            $penanggungJawabId = $this->getRandomPenanggungJawabForArea($areaId);
            
            Laporan::create([
                'tanggal' => $faker->dateTimeBetween('-2 months', 'now'),
                'area_id' => $areaId,
                'penanggung_jawab_id' => $penanggungJawabId,
                'departemen_supervisor_id' => 1,
                'problem_category_id' => $faker->randomElement($problemCategoryIds),
                'deskripsi_masalah' => $faker->paragraph(2),
                'tenggat_waktu' => $faker->dateTimeBetween('now', '+1 month'),
                'status' => 'Assigned',
                'created_at' => $faker->dateTimeBetween('-2 months', 'now'),
                'updated_at' => now()
            ]);
        }

        // Generate 40 laporan Completed dengan Penyelesaian
        for ($i = 0; $i < 40; $i++) {
            $areaId = $faker->randomElement($areaIds);
            $penanggungJawabId = $this->getRandomPenanggungJawabForArea($areaId);
            $createdAt = $faker->dateTimeBetween('-2 months', '-1 week');
            
            $laporan = Laporan::create([
                'tanggal' => $createdAt,
                'area_id' => $areaId,
                'penanggung_jawab_id' => $penanggungJawabId,
                'departemen_supervisor_id' => 1,
                'problem_category_id' => $faker->randomElement($problemCategoryIds),
                'deskripsi_masalah' => $faker->paragraph(2),
                'tenggat_waktu' => $faker->dateTimeBetween($createdAt, '+1 month'),
                'status' => 'Completed',
                'created_at' => $createdAt,
                'updated_at' => now()
            ]);

            // Buat penyelesaian untuk setiap laporan selesai
            Penyelesaian::create([
                'laporan_id' => $laporan->id,
                'tanggal' => $faker->dateTimeBetween($laporan->created_at, 'now'),
                'deskripsi_penyelesaian' => $faker->paragraph(2),
                'created_at' => $faker->dateTimeBetween($laporan->created_at, 'now'),
                'updated_at' => now()
            ]);
        }
    }


    /**
     * Get random penanggung jawab for specific area
     */
    private function getRandomPenanggungJawabForArea($areaId)
    {
        $penanggungJawabIds = PenanggungJawab::where('area_id', $areaId)->pluck('id')->toArray();
        return $penanggungJawabIds[array_rand($penanggungJawabIds)];
    }

}