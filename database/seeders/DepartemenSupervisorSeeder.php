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
                'email' => 'sirjotaro666@gmail.com',
            ],
            [
                'id' => 2,
                'departemen' => 'LV Box',
                'supervisor' => 'Rachmad Haryono',
                'workgroup' => 'SI EA O AIS JKT MF AS LVB',
                'email' => null,
            ],
            [
                'id' => 3,
                'departemen' => 'MV Assembly',
                'supervisor' => 'Helmy Sundani',
                'workgroup' => 'SI EA O AIS JKT MF AS MV',
                'email' => null,
            ],
            [
                'id' => 4,
                'departemen' => 'Prefabrication',
                'supervisor' => 'Sarifudin Raysan',
                'workgroup' => 'SI EA O AIS JKT MF PR',
                'email' => null,
            ],
            [
                'id' => 5,
                'departemen' => 'Packing',
                'supervisor' => 'Bayu Putra Trianto',
                'workgroup' => 'SI EA O AIS JKT MF MS PKG',
                'email' => null,
            ],
            [
                'id' => 6,
                'departemen' => 'Tool Store',
                'supervisor' => 'Joni Rahman',
                'workgroup' => 'SI EA O AIS JKT MF MS FS',
                'email' => null,
            ],
            [
                'id' => 7,
                'departemen' => 'QC LV',
                'supervisor' => 'Ishak Marthen',
                'workgroup' => 'SI EA O AIS JKT QC LV',
                'email' => null,
            ],
            [
                'id' => 8,
                'departemen' => 'QC MV',
                'supervisor' => 'Sirad Nova Mihardi',
                'workgroup' => 'SI EA O AIS JKT QC MV',
                'email' => null,
            ],
            [
                'id' => 9,
                'departemen' => 'IQC',
                'supervisor' => 'Abduh Al Agani',
                'workgroup' => 'SI EA O AIS JKT QC',
                'email' => null,
            ],
            [
                'id' => 10,
                'departemen' => 'LV Module',
                'supervisor' => 'Hadi Djohansyah',
                'workgroup' => 'SI EA O AIS JKT MF AS LVM',
                'email' => null,
            ],
            [
                'id' => 11,
                'departemen' => 'Warehouse',
                'supervisor' => 'Suhendra',
                'workgroup' => 'SI EA O AIS JKT LOG WH',
                'email' => null,
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