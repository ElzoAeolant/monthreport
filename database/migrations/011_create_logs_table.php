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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tracking_id');
            $table->timestamp('timestamp')->useCurrent ();
            $table->float('indoor_temperature',5,2);
            $table->float('indoor_humidity',5,2);
            $table->float('outdoor_temperature',5,2);
            $table->float('outdoor_humidity',5,2);
            $table->timestamps();

            $table->foreign('tracking_id')->references('id')->on('trackings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logs');
    }
};
