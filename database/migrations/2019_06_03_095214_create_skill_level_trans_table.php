<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSkillLevelTransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skill_level_trans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('skill_level_id')->unsigned()->nullable();
            $table->foreign('skill_level_id')->references('id')->on('skill_levels');

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
        Schema::dropIfExists('skill_level_trans');
    }
}
