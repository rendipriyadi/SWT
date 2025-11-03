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
     * 
     * Generates sample reports with the following distribution:
     * - 45 Assigned reports with specific PIC
     * - 15 Assigned reports without specific PIC (area only)
     * - 30 Completed reports with specific PIC
     * - 10 Completed reports without specific PIC (area only)
     * 
     * Total: 100 reports (60 Assigned, 40 Completed)
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

        // Generate 45 laporan Assigned with specific PIC
        for ($i = 0; $i < 45; $i++) {
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

        // Generate 15 laporan Assigned WITHOUT specific PIC (area only)
        for ($i = 0; $i < 15; $i++) {
            $areaId = $faker->randomElement($areaIds);
            
            Laporan::create([
                'tanggal' => $faker->dateTimeBetween('-2 months', 'now'),
                'area_id' => $areaId,
                'penanggung_jawab_id' => null, // No specific PIC
                'departemen_supervisor_id' => 1,
                'problem_category_id' => $faker->randomElement($problemCategoryIds),
                'deskripsi_masalah' => $faker->paragraph(2),
                'tenggat_waktu' => $faker->dateTimeBetween('now', '+1 month'),
                'status' => 'Assigned',
                'created_at' => $faker->dateTimeBetween('-2 months', 'now'),
                'updated_at' => now()
            ]);
        }

        // Generate 30 laporan Completed with specific PIC
        for ($i = 0; $i < 30; $i++) {
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

        // Generate 10 laporan Completed WITHOUT specific PIC (area only)
        for ($i = 0; $i < 10; $i++) {
            $areaId = $faker->randomElement($areaIds);
            $createdAt = $faker->dateTimeBetween('-2 months', '-1 week');
            
            $laporan = Laporan::create([
                'tanggal' => $createdAt,
                'area_id' => $areaId,
                'penanggung_jawab_id' => null, // No specific PIC
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