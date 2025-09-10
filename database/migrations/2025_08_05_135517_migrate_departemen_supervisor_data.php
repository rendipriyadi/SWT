<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\DepartemenSupervisor;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Insert areas data
        $areaData = [
            ['id' => 1, 'name' => 'Manufaktur', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'QC', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Warehouse', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('areas')->insert($areaData);

        // 2. Insert penanggung_jawab data
        $penanggungjawabData = [
            // Area 1: Manufaktur
            ['id' => 1, 'area_id' => 1, 'station' => 'LV Assembly', 'name' => 'Aris Setiawan', 'email' => 'sirjotaro666@gmail.com', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'area_id' => 1, 'station' => 'LV Box', 'name' => 'Rachmad Haryono', 'email' => 'sirjotaro666@gmail.com', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'area_id' => 1, 'station' => 'LV Module', 'name' => 'Hadi Djohansyah', 'email' => 'sirjotaro666@gmail.com', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'area_id' => 1, 'station' => 'MV Assembly', 'name' => 'Helmy Sundani', 'email' => 'sirjotaro666@gmail.com', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'area_id' => 1, 'station' => 'Prefabrication', 'name' => 'Sarifudin Raysan', 'email' => 'sirjotaro666@gmail.com', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'area_id' => 1, 'station' => 'Packing', 'name' => 'Bayu Putra Trianto', 'email' => 'sirjotaro666@gmail.com', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'area_id' => 1, 'station' => 'Tool Store', 'name' => 'Joni Rahman', 'email' => 'sirjotaro666@gmail.com', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'area_id' => 1, 'station' => 'General', 'name' => 'Tri Widardi', 'email' => 'sirjotaro666@gmail.com', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'area_id' => 1, 'station' => 'General', 'name' => 'Asept Surachaman', 'email' => 'sirjotaro666@gmail.com', 'created_at' => now(), 'updated_at' => now()],
            
            // Area 2: QC
            ['id' => 10, 'area_id' => 2, 'station' => 'QC LV', 'name' => 'Ishak Marthen', 'email' => 'sirjotaro666@gmail.com', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'area_id' => 2, 'station' => 'QC MV', 'name' => 'Sirad Nova Mihardi', 'email' => 'sirjotaro666@gmail.com', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'area_id' => 2, 'station' => 'IQC', 'name' => 'Abduh Al Agani', 'email' => 'sirjotaro666@gmail.com', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 13, 'area_id' => 2, 'station' => 'General', 'name' => 'Arif Hadi Rizali', 'email' => 'sirjotaro666@gmail.com', 'created_at' => now(), 'updated_at' => now()],
            
            // Area 3: Warehouse
            ['id' => 14, 'area_id' => 3, 'station' => 'Warehouse', 'name' => 'Suhendra', 'email' => 'sirjotaro666@gmail.com', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 15, 'area_id' => 3, 'station' => 'Warehouse', 'name' => 'Wahyu Wahyudin', 'email' => 'sirjotaro666@gmail.com', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('penanggung_jawab')->insert($penanggungjawabData);

        // 3. Mapping departemen_supervisors ID ke struktur baru
        $mapping = [
            // Areas
            1 => ['area_id' => 1, 'penanggung_jawab_id' => null], // Manufaktur
            2 => ['area_id' => 2, 'penanggung_jawab_id' => null], // QC
            3 => ['area_id' => 3, 'penanggung_jawab_id' => null], // Warehouse
            
            // Stations
            4 => ['area_id' => 1, 'penanggung_jawab_id' => 1],  // LV Assembly
            5 => ['area_id' => 1, 'penanggung_jawab_id' => 2],  // LV Box
            6 => ['area_id' => 1, 'penanggung_jawab_id' => 3],  // LV Module
            7 => ['area_id' => 1, 'penanggung_jawab_id' => 4],  // MV Assembly
            8 => ['area_id' => 1, 'penanggung_jawab_id' => 5],  // Prefabrication
            9 => ['area_id' => 1, 'penanggung_jawab_id' => 6],  // Packing
            10 => ['area_id' => 1, 'penanggung_jawab_id' => 7], // Tool Store
            11 => ['area_id' => 2, 'penanggung_jawab_id' => 10], // QC LV
            12 => ['area_id' => 2, 'penanggung_jawab_id' => 11], // QC MV
            13 => ['area_id' => 2, 'penanggung_jawab_id' => 12], // IQC
        ];

        // 4. Migrasi data laporan
        foreach ($mapping as $old_id => $new_ids) {
            DB::table('laporan')
                ->where('departemen_supervisor_id', $old_id)
                ->update([
                    'area_id' => $new_ids['area_id'],
                    'penanggung_jawab_id' => $new_ids['penanggung_jawab_id']
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Rollback data laporan
        DB::table('laporan')
            ->whereNotNull('area_id')
            ->orWhereNotNull('penanggung_jawab_id')
            ->update([
                'area_id' => null,
                'penanggung_jawab_id' => null
            ]);
            
        // 2. Truncate data dari tabel baru
        DB::table('penanggung_jawab')->truncate();
        DB::table('areas')->truncate();
    }
};
