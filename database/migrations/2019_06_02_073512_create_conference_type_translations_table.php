<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConferenceTypeTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {  Schema::create('conference_type_translations', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('conference_type_id')->unsigned()->nullable();
        $table->foreign('conference_type_id')->references('id')->on('conference_types');

        $table->integer('translated_languages_id')->unsigned()->nullable();
        $table->foreign('translated_languages_id')->references('id')->on('translated_languages');

        $table->string('name');

    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('conference_type_translations');
    }
}
