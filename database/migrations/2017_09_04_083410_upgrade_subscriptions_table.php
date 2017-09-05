<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpgradeSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('remote_status')->nullable();
            $table->string('buyer_email')->nullable();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('buyer_email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['remote_status', 'buyer_email']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['buyer_email']);
        });
    }
}
