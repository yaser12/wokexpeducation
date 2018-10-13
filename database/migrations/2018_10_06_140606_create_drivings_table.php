<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDrivingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('license_type')->nullable();
            $table->string('country')->nullable();
            $table->boolean('international')->nullable();
            $table->integer('resume_id')->unsigned();
            $table->foreign('resume_id')->references('id')->on('resumes')->onDelete('cascade');;
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
        Schema::dropIfExists('drivings');
    }
}
