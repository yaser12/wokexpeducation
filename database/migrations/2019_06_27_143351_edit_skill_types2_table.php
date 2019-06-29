<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditSkillTypes2Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('skills', function (Blueprint $table) {
//            $table->dropForeign('skills_skill_type_id_foreign');
//            $table->dropColumn('skill_type_id');
            $table->integer('skill_type_id')->unsigned()->nullable();
            $table->foreign('skill_type_id')->references('id')->on('skill_types')->onDelete('cascade');
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
