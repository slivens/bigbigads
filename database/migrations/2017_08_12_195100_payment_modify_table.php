<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PaymentModifyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->renameColumn('remote_id', 'paypal_id');
            $table->float('setup_fee')->nullable();
            $table->integer('delay_days')->default(0);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('gateway');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->renameColumn('paypal_id', 'remote_id');
            $table->dropColumn(['setup_fee', 'delay_days']);
        });


        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('gateway');
        });
    }
}
