<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditCertificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //certifications
        Schema::table('certifications', function (Blueprint $table) {
            $table->integer('valid_year_id')->unsigned()->nullable();
            $table->foreign('valid_year_id')->references('id')->on('valid_years');




        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
