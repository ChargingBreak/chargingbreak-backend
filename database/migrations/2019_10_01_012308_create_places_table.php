<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('places', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('place_id')->index()->nullable();
            $table->string('name')->index()->nullable();

            $table->unsignedBigInteger('spider_id');
            $table->foreign('spider_id')->references('id')->on('place_spiders');

            $table->unsignedInteger('charger_id');
            $table->foreign('charger_id')->references('id')->on('chargers');

            $table->integer('distance_meters')->index()->nullable();

            $table->string('address_full')->nullable();
            $table->string('address_housenumber')->nullable();
            $table->string('address_street')->nullable();
            $table->string('address_city')->nullable();
            $table->string('address_state')->nullable();
            $table->string('address_zip')->nullable();
            $table->string('address_country')->nullable();
            $table->point('coordinate');

            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->string('opening_hours')->nullable();

            $table->timestamps();

            $table->spatialIndex('coordinate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('places');
    }
}
