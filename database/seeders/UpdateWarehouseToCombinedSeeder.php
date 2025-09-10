<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DepartemenSupervisor;

class UpdateWarehouseToCombinedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cari departemen Warehouse
        $warehouse = DepartemenSupervisor::where('departemen', 'Warehouse')->first();
        
        if ($warehouse) {
            // Update menjadi departemen gabungan
            $warehouse->update([
                'is_group' => true,
                'supervisor' => 'Koordinator Warehouse', // Supervisor utama/koordinator
                'group_members' => ['Pa Suhendra', 'Pa Wahyu']
            ]);
            
            $this->command->info('Departemen Warehouse berhasil diubah menjadi departemen gabungan.');
        } else {
            // Jika departemen belum ada, buat baru
            DepartemenSupervisor::create([
                'departemen' => 'Warehouse',
                'supervisor' => 'Koordinator Warehouse',
                'email' => 'warehouse@example.com', // Email untuk notifikasi
                'is_group' => true,
                'group_members' => ['Pa Suhendra', 'Pa Wahyu']
            ]);
            
            $this->command->info('Departemen Warehouse berhasil ditambahkan sebagai departemen gabungan.');
        }
    }
}