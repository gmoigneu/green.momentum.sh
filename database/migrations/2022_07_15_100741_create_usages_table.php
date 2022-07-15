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
        Schema::create('usages', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string("countryCode");
            $table->decimal("carbonIntensity", 4,14);
            $table->decimal("fossilFuelPercentage", 3,14);
            $table->string("units");

            $table->unsignedBigInteger('datacenter_id');
            $table->foreign('datacenter_id')->references('id')->on('datacenters');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usages');
    }
};
