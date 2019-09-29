<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChargersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chargers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('location_id')->index()->nullable();
            $table->string('name')->nullable();
            $table->string('status')->index()->nullable();
            $table->string('address_street')->nullable();
            $table->string('address_city')->nullable();
            $table->string('address_state')->nullable();
            $table->string('address_zip')->nullable();
            $table->string('address_country_id')->nullable();
            $table->string('address_country')->nullable();
            $table->string('address_region_id')->nullable();
            $table->string('address_region')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 9)->nullable();
            $table->date('date_opened')->nullable();
            $table->integer('stall_count')->nullable();
            $table->boolean('counted')->nullable();
            $table->integer('elevation_meters')->nullable();
            $table->integer('power_kilowatt')->nullable();
            $table->boolean('solar_canopy')->nullable();
            $table->boolean('battery')->nullable();
            $table->integer('status_days')->nullable();
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
        Schema::dropIfExists('chargers');
    }
}
