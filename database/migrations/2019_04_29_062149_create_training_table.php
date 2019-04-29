<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrainingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trainings', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('resume_id')->unsigned();
            $table->string('name');
            $table->string('organization')->nullable();
            $table->date('from')->nullable();
            $table->date('to')->nullable();
            $table->boolean('isPresent')->nullable();
            $table->string('isFromMonthPresent')->nullable();
            $table->string('isToMonthPresent')->nullable();
            $table->string('total_hours')->nullable();
            $table->string('website')->nullable();
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->foreign('resume_id')->references('id')->on('resumes')->onDelete('cascade');
            $table->integer('order');
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
        Schema::dropIfExists('trainings');
    }
}
