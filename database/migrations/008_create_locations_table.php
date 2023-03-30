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

        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->nullable();
            $table->string('nombre')->nullable();
            $table->string('activo')->nullable();
            $table->string('abrev')->nullable();
            $table->timestamps();
        });

        Schema::create('municipalities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->string('clave')->nullable();
            $table->string('nombre')->nullable();
            $table->string('activo')->nullable();
            $table->string('ssid_password')->nullable();
            $table->float('latitude', 20 , 8)->nullable();
            $table->float('longitude', 20 , 8)->nullable();
            $table->timestamps();

            $table->foreign('state_id')->references('id')->on('states');
        });

        Schema::create('towns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('municipality_id')->nullable();
            $table->string('clave',4)->nullable();
            $table->string('nombre',100)->nullable();
            $table->integer('mapa')->nullable();
            $table->string('ambito',1)->nullable();
            $table->string('latitud')->nullable();
            $table->string('longitud')->nullable();
            $table->float('lat',10,7)->nullable();
            $table->float('lng',10,7)->nullable();
            $table->string('altitud')->nullable();
            $table->string('carta')->nullable();
            $table->string('poblacion')->nullable();
            $table->string('masculino')->nullable();
            $table->string('femenino')->nullable();
            $table->string('viviendas')->nullable();
            $table->string('activo')->nullable();
            $table->timestamps();

            $table->foreign('municipality_id')->references('id')->on('municipalities');
        });

        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cluster_id')->nullable();
            $table->unsignedBigInteger('town_id')->nullable();
            $table->unsignedBigInteger('municipality_id')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->string('tag')->nullable();
            $table->string('ssid')->nullable();
            $table->string('description')->nullable();
            $table->string('ssid_password')->nullable();
            $table->float('latitude', 8 , 2)->nullable();
            $table->float('longitude', 8 , 2)->nullable();
            $table->timestamps();

            $table->foreign('town_id')->references('id')->on('towns');
            $table->foreign('municipality_id')->references('id')->on('municipalities');
            $table->foreign('state_id')->references('id')->on('states');
            $table->foreign('cluster_id')->references('id')->on('tags');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locations');
        Schema::dropIfExists('towns');
        Schema::dropIfExists('municipalities');
        Schema::dropIfExists('states');
        
        
    }
};
