<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Buat tabel areas
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->timestamps();
        });
        
        // Buat tabel penanggung_jawab
        Schema::create('penanggung_jawab', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained('areas');
            $table->string('station', 255);
            $table->string('name', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus tabel dengan urutan terbalik untuk menghindari masalah foreign key
        Schema::dropIfExists('penanggung_jawab');
        Schema::dropIfExists('areas');
    }
};
