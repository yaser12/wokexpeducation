<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {

            $table->increments('id');
//            $table->integer('work_experience_id')->unsigned();
            $table->string('name');
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('company_size')->nullable();
            $table->string('company_website')->nullable();
            $table->string('company_description')->nullable();
            $table->boolean('verified_by_google')->default(false);
            $table->timestamps();
//            $table->foreign('work_experience_id')->references('id')->on('work_experiences')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
