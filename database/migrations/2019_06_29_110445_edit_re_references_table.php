<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditReReferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('re_references', function (Blueprint $table) {
            $table->dropForeign('re_references_country_id_foreign');
            $table->dropColumn('country_id');
            $table->dropColumn('name');
            $table->dropColumn('position');
            $table->dropColumn('organization');
            $table->dropColumn('mobile');
            $table->dropColumn('ref_email_address');
            $table->dropColumn('prefered_time_to_call');

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
