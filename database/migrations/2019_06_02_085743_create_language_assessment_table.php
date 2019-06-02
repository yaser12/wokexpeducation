<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLanguageAssessmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('language_assessments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('language_id')->unsigned()->nullable();
            $table->foreign('language_id')->references('id')->on('languages');

            $table->integer('self_assessment_id')->unsigned()->nullable();
            $table->foreign('self_assessment_id')->references('id')->on('self_assessments');

            $table->string('assessment_type');
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
      Schema::dropIfExists('language_assessments');
    }
}
