<?php
// Migration to update department-supervisor structure
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\DepartemenSupervisor;
use Illuminate\Support\Facades\DB;
use App\Models\laporan;

class UpdateDepartmentSupervisorData extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, update existing laporan records to prevent orphaned records
        // Set all existing records to a temporary value
        laporan::query()->update(['departemen_supervisor_id' => null]);
        
        // Temporarily disable foreign key checks to allow truncation
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clear existing data
        DepartemenSupervisor::truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Insert area groups with specific IDs
        DepartemenSupervisor::create([
            'id' => 1, // manufaktur
            'departemen' => 'Manufaktur',
            'supervisor' => 'Manufaktur Team',
            'is_group' => true,
            'group_members' => ['Aris Setiawan', 'Rachmad Haryono', 'Hadi Djohansyah', 'Helmy Sundani', 
                              'Sarifudin Raysan', 'Bayu Putra Trianto', 'Joni Rahman', 'Tri Widardi', 'Asept Surachaman']
        ]);
        
        DepartemenSupervisor::create([
            'id' => 2, // qc
            'departemen' => 'QC',
            'supervisor' => 'QC Team',
            'is_group' => true,
            'group_members' => ['Ishak Marthen', 'Sirad Nova Mihardi', 'Arif Hadi Rizali']
        ]);
        
        DepartemenSupervisor::create([
            'id' => 3, // warehouse
            'departemen' => 'Warehouse',
            'supervisor' => 'Warehouse Team',
            'is_group' => true,
            'group_members' => ['Suhendra', 'Wahyu Wahyudin']
        ]);
        
        // Insert individual stations with specific IDs
        $stations = [
            [4, 'LV Assembly', 'Aris Setiawan'],
            [5, 'LV Box', 'Rachmad Haryono'],
            [6, 'LV Module', 'Hadi Djohansyah'],
            [7, 'MV Assembly', 'Helmy Sundani'],
            [8, 'Prefabrication', 'Sarifudin Raysan'],
            [9, 'Packing', 'Bayu Putra Trianto'],
            [10, 'Tool Store', 'Joni Rahman'],
            [11, 'QC LV', 'Ishak Marthen'],
            [12, 'QC MV', 'Sirad Nova Mihardi'],
            [13, 'IQC', 'Abduh Al Agani']
        ];
        
        foreach ($stations as $station) {
            DepartemenSupervisor::create([
                'id' => $station[0],
                'departemen' => $station[1],
                'supervisor' => $station[2],
                'is_group' => false
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Disable foreign key checks for rollback
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Rollback option
        DepartemenSupervisor::truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
