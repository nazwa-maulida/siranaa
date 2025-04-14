<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProyeksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proyeks', function (Blueprint $table) {
            $table->bigIncrements('ProyekID');
            $table->unsignedBigInteger('MitraID');
            $table->unsignedBigInteger('PerusahaanID')->nullable(); // Kolom ini dibuat nullable
            $table->string('Judul');
            $table->string('Deskripsi');
            $table->string('Lokasi');
            $table->enum('Status', ['Diajukan', 'Diambil', 'Disurvey', 'Disetujui', 'Ditolak', 'Dikerjakan', 'Selesai', 'Tidak Selesai'])->default('Diajukan');
            $table->boolean('IsResubmitted')->default(false); // Tambahkan kolom IsResubmitted
            $table->date('TglPengajuan');
            $table->timestamps();
            
            // Foreign key untuk MitraID dan PerusahaanID
            $table->foreign('MitraID')->references('MitraID')->on('mitras')->onDelete('cascade');
            $table->foreign('PerusahaanID')->references('PerusahaanID')->on('perusahaans')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proyeks');
    }
}
