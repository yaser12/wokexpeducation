<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditSkillTypes3Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('skill_types', function (Blueprint $table) {
            $table->dropForeign('skill_types_skill_type_parents_id_foreign');
            $table->dropColumn('skill_type_parents_id');
            $table->integer('skill_type_parent_id')->unsigned()->nullable();
            $table->foreign('skill_type_parent_id')->references('id')->on('skill_type_parents')->onDelete('cascade');
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
