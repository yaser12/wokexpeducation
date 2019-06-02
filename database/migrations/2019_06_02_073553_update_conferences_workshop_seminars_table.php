<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateConferencesWorkshopSeminarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('conferences_workshop_seminars', function (Blueprint $table) {
            $table->integer('conference_type_id')->unsigned()->nullable();
            $table->foreign('conference_type_id')->references('id')->on('conference_types');


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
