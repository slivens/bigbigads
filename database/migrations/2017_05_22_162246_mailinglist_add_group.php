<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MailinglistAddGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('maillist', function (Blueprint $table) {
            $table->string('category')->nullable();
            $table->string('email')->nullable()->change();
            $table->dropUnique('maillist_email_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('maillist', function (Blueprint $table) {
            $table->dropColumn('category');
            $table->unique('email');
        });
    }
}
