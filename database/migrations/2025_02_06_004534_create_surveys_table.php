<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surveys', function (Blueprint $table) {
            $table->bigIncrements('SurveyID');
            $table->unsignedBigInteger('ProyekID');
            $table->unsignedBigInteger('PerusahaanID');
            $table->date('TglSurvey');
            $table->string('Catatan');
            $table->enum('Keputusan', ['Pending','Lanjut', 'Tidak Lanjut'])->default('Pending');
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
        Schema::dropIfExists('surveys');
    }
}
