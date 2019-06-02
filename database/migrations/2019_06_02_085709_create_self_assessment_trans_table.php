<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSelfAssessmentTransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('self_assessment_trans', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('self_assessment_id')->unsigned()->nullable();
            $table->foreign('self_assessment_id')->references('id')->on('self_assessments');


            $table->integer('translated_languages_id')->unsigned()->nullable();
            $table->foreign('translated_languages_id')->references('id')->on('translated_languages');

            $table->string('name');
            ;
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
        Schema::dropIfExists('self_assessment_trans');
    }
}
