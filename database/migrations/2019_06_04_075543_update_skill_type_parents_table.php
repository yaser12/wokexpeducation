<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateSkillTypeParentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('skill_type_parents', function (Blueprint $table) {
            $table->integer('skill_type_basic_parent_id')->unsigned()->nullable();
            $table->foreign('skill_type_basic_parent_id')->references('id')->on  ('skill_type_basic_parents');


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
