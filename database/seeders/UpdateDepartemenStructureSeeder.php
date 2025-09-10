<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DepartemenSupervisor;

class UpdateDepartemenStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Perbarui atau buat departemen Manufaktur
        $this->updateOrCreateDepartemen(
            'Manufaktur',
            'Koordinator Manufaktur',
            'manufaktur@example.com',
            [
                'Asep', 
                'Tri Widardi', 
                'MV Assembly', 
                'LV Assembly', 
                'LV Box', 
                'Module', 
                'Packing', 
                'Prefabrication'
            ]
        );

        // Perbarui atau buat departemen LV
        $this->updateOrCreateDepartemen(
            'LV',
            'Koordinator LV',
            'lv@example.com',
            [
                'LV Assembly',
                'LV Box',
                'LV Module'
            ]
        );

        // Perbarui atau buat departemen QC
        $this->updateOrCreateDepartemen(
            'QC',
            'Koordinator QC',
            'qc@example.com',
            [
                'QC LV',
                'QC MV'
            ]
        );
    }

    /**
     * Helper untuk memperbarui atau membuat departemen dengan format yang konsisten
     */
    private function updateOrCreateDepartemen($name, $supervisor, $email, $members)
    {
        $departemen = DepartemenSupervisor::where('departemen', $name)->first();
        
        $data = [
            'departemen' => $name,
            'supervisor' => $supervisor,
            'email' => $email,
            'is_group' => true,
            'group_members' => $members
        ];
        
        if ($departemen) {
            $departemen->update($data);
            $this->command->info("Departemen {$name} berhasil diperbarui.");
        } else {
            DepartemenSupervisor::create($data);
            $this->command->info("Departemen {$name} berhasil ditambahkan.");
        }
    }
}