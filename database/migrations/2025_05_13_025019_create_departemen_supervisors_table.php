<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('departemen_supervisors', function (Blueprint $table) {
            $table->id();
            $table->string('departemen');
            $table->string('supervisor');
            $table->string('workgroup')->nullable();
            $table->timestamps();
        });

        // Data awal departemen dan supervisor
        DB::table('departemen_supervisors')->insert([
            ['departemen' => 'LV Assembly', 'supervisor' => 'Aris Setiawan'],
            ['departemen' => 'LV Box', 'supervisor' => 'Rachmad Haryono'],
            ['departemen' => 'MV Assembly', 'supervisor' => 'Helmy Sundani'],
            ['departemen' => 'Prefabrication', 'supervisor' => 'Sarifudin Raysan'],
            ['departemen' => 'Packing', 'supervisor' => 'Bayu Putra Trianto'],
            ['departemen' => 'Tool Store', 'supervisor' => 'Joni Rahman'],
            ['departemen' => 'QC LV', 'supervisor' => 'Ishak Marthen'],
            ['departemen' => 'QC MV', 'supervisor' => 'Sirad Nova Mihardi'],
            ['departemen' => 'IQC', 'supervisor' => 'Abduh Al Afgani'],
            ['departemen' => 'LV Module', 'supervisor' => 'Hadi Djohansyah'],
            ['departemen' => 'Warehouse', 'supervisor' => 'Suhendra']
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departemen_supervisors');
    }
};
