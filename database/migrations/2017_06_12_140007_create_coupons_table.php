<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('code', 192)->unique();
            $table->integer('type')->default(0);
            $table->float('discount');
            $table->float('total')->nullable();
            $table->dateTime('start')->nullable();
            $table->dateTime('end')->nullable();
            $table->integer('uses')->default(0);
            $table->integer('customer_uses')->default(0);
            $table->integer('used')->default(0);
            $table->integer('status')->default(0);
            $table->timestamps();
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->integer('coupon_id')->nullable();
            $table->float('setup_fee')->nullable();
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
            $table->dropColumn(['coupon_id', 'setup_fee']);
        });
        Schema::dropIfExists('coupons');
    }
}
