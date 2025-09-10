<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DepartemenSupervisor;

class ManufakturDepartemenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cek apakah departemen Manufaktur sudah ada
        $existingDepartemen = DepartemenSupervisor::where('departemen', 'Manufaktur')->first();
        
        if (!$existingDepartemen) {
            // Tambahkan departemen Manufaktur jika belum ada
            DepartemenSupervisor::create([
                'departemen' => 'Manufaktur',
                'supervisor' => 'Koordinator Manufaktur', // Nama supervisor utama
                'email' => 'manufaktur@example.com', // Ganti dengan email yang sesuai
                'is_group' => true, // Kategori Gabungan
                'group_members' => [
                    'Asep', 
                    'Tri Widardi', 
                    'MV Assembly', 
                    'LV Assembly', 
                    'LV Box', 
                    'Module', 
                    'Packing', 
                    'Prefabrication'
                ]
            ]);
            
            $this->command->info('Departemen Manufaktur berhasil ditambahkan.');
        } else {
            $this->command->info('Departemen Manufaktur sudah ada.');
        }
    }
}