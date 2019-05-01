<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReReferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('re_references', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('position')->nullable();
            $table->string('organization')->nullable();
            $table->string('mobile')->nullable();
            $table->string('country_code')->nullable();
            $table->string('ref_email_address')->nullable();
            $table->string('prefered_time_to_call')->nullable();
            $table->string('is_available')->nullable();
            $table->integer('order');
            $table->integer('resume_id')->unsigned();
            $table->foreign('resume_id')->references('id')->on('resumes')->onDelete('cascade');
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
        Schema::dropIfExists('re_references');
    }
}
