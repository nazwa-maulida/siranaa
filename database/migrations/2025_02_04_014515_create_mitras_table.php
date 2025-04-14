<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMitrasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mitras', function (Blueprint $table) {
            $table->bigIncrements('MitraID');
            $table->unsignedBigInteger('user_id')->nullable(); // Membuat user_id nullable
            $table->string('NamaMitra', 150);
            $table->string('PIC', 100);
            $table->string('NoTelp', 15);
            $table->string('Alamat', 150);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mitras');
    }
}
