<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRekomendasisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rekomendasis', function (Blueprint $table) {
            $table->bigIncrements('RekomendasiID');
            $table->unsignedBigInteger('ProyekID');
            $table->unsignedBigInteger('PerusahaanID');
            $table->unsignedBigInteger('MitraID'); // Menambahkan field MitraID
            $table->string('Catatan');
            $table->string('FileAnggaran');
            $table->string('FileSPK')->nullable(); // SPK bisa null jika belum diupload
            $table->string('CatatanMitra')->nullable();
            $table->enum('Status', ['menunggu persetujuan','anggaran ditolak','anggaran diperbarui', 'spk terbit'])->default('menunggu persetujuan');
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
        Schema::dropIfExists('rekomendasis');
    }
}
