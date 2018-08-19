<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlaceOfBirthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('place_of_births', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('personal_information_id')->unsigned();
            $table->string('country')->nullble();
            $table->string('city')->nullble();
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
        Schema::dropIfExists('place_of_births');
    }
}
