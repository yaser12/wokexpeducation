<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSkillTypeBasicParentTransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skill_type_basic_parent_trans', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('skill_type_basic_parent_id')->unsigned()->nullable();
            $table->foreign('skill_type_basic_parent_id')->references('id')->on('skill_type_basic_parents');


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
        Schema::dropIfExists('skill_type_basic_parent_trans');
    }
}
