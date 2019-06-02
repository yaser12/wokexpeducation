<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInternationalLanguageTransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('international_language_trans', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('international_language_id')->unsigned()->nullable();
            $table->foreign('international_language_id')->references('id')->on('international_languages');


            $table->integer('translated_languages_id')->unsigned()->nullable();
            $table->foreign('translated_languages_id')->references('id')->on('translated_languages');

            $table->string('name');



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
        Schema::dropIfExists('international_language_trans');
    }
}
