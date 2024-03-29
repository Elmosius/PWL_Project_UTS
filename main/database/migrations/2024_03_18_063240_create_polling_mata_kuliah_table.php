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
        Schema::create('polling_mata_kuliah', function (Blueprint $table) {
            $table->string('id_polling_mataKuliah',5)->primary();
            $table->string('id_mataKuliah',10);
            $table->foreign('id_mataKuliah')->references('id_mataKuliah')->on('mata_kuliah');
            $table->integer('jumlah_mahasiswa');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('polling_mata_kuliahs');
    }
};
