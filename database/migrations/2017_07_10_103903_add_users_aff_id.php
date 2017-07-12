<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUsersAffId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('aff_id')->nullable()->unsigned()->comment('推荐码');
            $table->foreign('aff_id')->references('id')->on('affiliates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table->dropForeign('users_aff_id_foreign');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('aff_id');
        });
    }
}
