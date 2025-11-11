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
        $supervisors = [
            [
                'id' => 1,
                'departemen' => 'LV Assembly',
                'supervisor' => 'Aris Setiawan',
                'workgroup' => 'SI EA O AIS JKT MD AS LV',
            ],
            [
                'id' => 2,
                'departemen' => 'LV Box',
                'supervisor' => 'Rachmad Haryono',
                'workgroup' => 'SI EA O AIS JKT MF AS LVB',
            ],
            [
                'id' => 3,
                'departemen' => 'MV Assembly',
                'supervisor' => 'Helmy Sundani',
                'workgroup' => 'SI EA O AIS JKT MF AS MV',
            ],
            [
                'id' => 4,
                'departemen' => 'Prefabrication',
                'supervisor' => 'Sarifudin Raysan',
                'workgroup' => 'SI EA O AIS JKT MF PR',
            ],
            [
                'id' => 5,
                'departemen' => 'Packing',
                'supervisor' => 'Bayu Putra Trianto',
                'workgroup' => 'SI EA O AIS JKT MF MS PKG',
            ],
            [
                'id' => 6,
                'departemen' => 'Tool Store',
                'supervisor' => 'Joni Rahman',
                'workgroup' => 'SI EA O AIS JKT MF MS FS',
            ],
            [
                'id' => 7,
                'departemen' => 'QC LV',
                'supervisor' => 'Ishak Marthen',
                'workgroup' => 'SI EA O AIS JKT QC LV',
            ],
            [
                'id' => 8,
                'departemen' => 'QC MV',
                'supervisor' => 'Sirad Nova Mihardi',
                'workgroup' => 'SI EA O AIS JKT QC MV',
            ],
            [
                'id' => 9,
                'departemen' => 'IQC',
                'supervisor' => 'Abduh Al Afgani',
                'workgroup' => 'SI EA O AIS JKT QC',
            ],
            [
                'id' => 10,
                'departemen' => 'LV Module',
                'supervisor' => 'Hadi Djohansyah',
                'workgroup' => 'SI EA O AIS JKT MF AS LVM',
            ],
            [
                'id' => 11,
                'departemen' => 'Warehouse',
                'supervisor' => 'Suhendra',
                'workgroup' => 'SI EA O AIS JKT LOG WH',
            ],
        ];

        foreach ($supervisors as $supervisorData) {
            DepartemenSupervisor::updateOrCreate(
                ['id' => $supervisorData['id']],
                $supervisorData
            );
        }
    }
}