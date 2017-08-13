<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PaymentUpgradeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('status');
            $table->renameColumn('payment_id', 'agreement_id');
            $table->integer('user_id')->unsigned()->change();

            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->renameColumn('plan', 'subscription_id');
            $table->renameColumn('currency_code', 'currency');
            $table->renameColumn('total_amount', 'amount');
        });

        Schema::create('refunds', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('payment_id');
            $table->float('amount');
            $table->string('status');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('payment_id')->references('id')->on('payments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('refunds');

        Schema::table('payments', function (Blueprint $table) {
            $table->renameColumn('amount', 'total_amount');
            $table->renameColumn('currency', 'currency_code');
            $table->renameColumn('subscription_id', 'plan');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign('subscriptions_user_id_foreign');
           // $table->integer('user_id')->change();
            $table->renameColumn('agreement_id', 'payment_id');
            $table->dropColumn('status');
        });

    }
}
