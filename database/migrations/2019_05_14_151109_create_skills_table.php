<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSkillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('resume_id')->unsigned();

            $table->string('skill_level')->nullable();

            $table->foreign('resume_id')->references('id')->on('resumes')->onDelete('cascade');

            $table->integer('skill_types_id')->unsigned();
            $table->foreign('skill_types_id')->references('id')->on('skill_types')->onDelete('cascade');

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
        Schema::dropIfExists('skills');
    }
}
