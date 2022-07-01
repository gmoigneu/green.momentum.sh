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
        Schema::create('datacenters', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->unsignedBigInteger('provider_id');
            $table->foreign('provider_id')->references('id')->on('providers');

            $table->string('provider_code');
            $table->string('provider_code_api')->nullable();
            $table->string('city')->nullable();
            $table->string('country_code')->nullable();
            $table->integer('facilities')->nullable();
            $table->double('lat');
            $table->double('long');
            $table->boolean('planned')->default(false);
            $table->boolean('special')->default(false);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('datacenters');
    }
};
