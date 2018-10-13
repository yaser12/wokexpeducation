<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('language_id')->unsigned();
            $table->integer('order')->unsigned();
            $table->string('type');
            $table->string('listening')->nullable();
            $table->string('reading')->nullable();
            $table->string('speaking')->nullable();
            $table->string('writing')->nullable();
            $table->integer('resume_id')->unsigned();
            $table->foreign('language_id')->references('id')->on('international_languages');
            $table->foreign('resume_id')->references('id')->on('resumes');
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
        Schema::dropIfExists('languages');
    }
}
