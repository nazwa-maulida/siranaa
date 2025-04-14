<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRealisasiProyeksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('realisasi_proyeks', function (Blueprint $table) {
            $table->bigIncrements('RPID');
            $table->unsignedBigInteger('ProyekID');
            $table->unsignedBigInteger('PerusahaanID');
            $table->date('TglMulai');
            $table->date('TglSelesai');
            $table->enum('Status', ['Dalam Proses', 'Selesai'])->default('Dalam Proses');
            $table->string('Catatan');
            $table->string('Dokumentasi')->nullable();;
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('realisasi_proyeks');
    }
}
