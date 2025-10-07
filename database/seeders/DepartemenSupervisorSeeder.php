<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DepartemenSupervisor;

class DepartemenSupervisorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DepartemenSupervisor::create([
            'id' => 1,
            'departemen' => 'Default Department',
            'supervisor' => 'Default Supervisor',
            'workgroup' => 'WG-DEFAULT',
            'email' => 'supervisor@siemens.com',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}