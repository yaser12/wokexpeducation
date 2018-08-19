<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCurrentLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('current_locations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('personal_information_id')->unsigned();
            $table->string('country')->nullble();
            $table->string('city')->nullble();
            $table->string('postal_code')->nullble();
            $table->string('street_address')->nullble();
            $table->decimal('latitude',18,15)->nullble();
            $table->decimal('longitude',18,15)->nullble();
            $table->timestamps();
            $table->foreign('personal_information_id')->references('id')->on('personal_informations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('current_locations');
    }
}
