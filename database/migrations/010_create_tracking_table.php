<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trackings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_id');
            $table->unsignedBigInteger('location_id');
            $table->unsignedBigInteger('user_id');
            $table->string('api_token', 64);
            $table->dateTime('installation_date')->useCurrent();
            $table->longText('image_device')->nullable();
            $table->longText('image_indoor')->nullable();
            $table->longText('image_outdoor')->nullable();
            $table->string('status', 15);
            $table->timestamps();
            $table->foreign('device_id')->references('id')->on('devices');
            $table->foreign('location_id')->references('id')->on('locations');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('device_trackings');
    }
};
