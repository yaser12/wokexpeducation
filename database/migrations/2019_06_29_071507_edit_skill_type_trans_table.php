<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditSkillTypeTransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('skill_type_trans', function (Blueprint $table) {
            $table->dropForeign('skill_type_trans_skill_type_id_foreign');
            $table->dropColumn('skill_types_id');
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
