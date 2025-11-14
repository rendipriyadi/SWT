<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
public function up(): void
{
    Schema::create('laporan', function (Blueprint $table) {
        $table->id();
        $table->timestamp('Tanggal')->default(DB::raw('CURRENT_TIMESTAMP'));
        $table->text('Foto')->nullable(); // Langsung TEXT (tidak perlu string dulu)
        $table->unsignedBigInteger('departemen_supervisor_id');
        $table->text('deskripsi_masalah');
        $table->date('tenggat_waktu');
        $table->string('status', 50)->default('Assigned'); // Sesuai dengan data actual
        $table->timestamps();

        $table->foreign('departemen_supervisor_id')
              ->references('id')
              ->on('departemen_supervisors')
              ->onDelete('cascade');
    });
}

    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};