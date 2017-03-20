<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //订阅全部从本地获取
        Schema::table('users', function ($table) {
            if (!Schema::hasColumn('users', 'subscription_id'))
                $table->integer('subscription_id')->nullable();
            if (!Schema::hasColumn('users', 'paypal_email'))
                $table->string('paypal_email')->nullable();
            if (!Schema::hasColumn('users', 'card_brand'))
                $table->string('card_brand')->nullable();
            if (!Schema::hasColumn('users', 'card_last_four'))
                $table->string('card_last_four')->nullable();
            if (!Schema::hasColumn('users', 'trial_ends_at'))
                $table->timestamp('trial_ends_at')->nullable();
		});
        //为了更接近cashier的设计，仍然使用subscriptions表名
		Schema::create('subscriptions', function ($table) {
			$table->increments('id');
			$table->integer('user_id');
			$table->string('plan');//plan id经常会变，所以使用plan名称作为索引
			$table->string('payment_id');//订单的唯一ID
			$table->integer('quantity');//数量,目前总为1
			$table->timestamp('trial_ends_at')->nullable();
			$table->timestamp('ends_at')->nullable();
			$table->timestamps();
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function ($table) {
            if (Schema::hasColumn('users', 'stripe_id'))
                $table->dropColumn('stripe_id');
            if (Schema::hasColumn('users', 'braintree_id'))
                $table->dropColumn('braintree_id');
			$table->dropColumn(['subscription_id', 'paypal_email', 'card_brand', 'card_last_four', 'trial_ends_at']);
		});
        Schema::dropIfExists('subscriptions');
    }
}
