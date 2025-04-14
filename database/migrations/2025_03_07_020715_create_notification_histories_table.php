<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationHistoriesTable extends Migration
{
    public function up()
    {
        Schema::create('notification_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('notification_id');
            $table->unsignedBigInteger('from_id');
            $table->unsignedBigInteger('to_id');
            $table->unsignedBigInteger('proyek_id')->nullable();
            $table->unsignedBigInteger('rekomendasi_id')->nullable();
            $table->string('type');
            $table->string('judul');
            $table->text('pesan');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('notification_histories');
    }
}