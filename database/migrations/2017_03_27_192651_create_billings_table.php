<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('uid');
            $table->string('tid', 100)->unique();
            $table->string('plan',100);
            $table->string('amount',45);
            $table->string('currency',20);
            $table->string('payer_email',100);
            $table->string('type', 100);
            $table->dateTime('startDate');
            $table->dateTime('endDate');
            $table->string('status',100);
            $table->string('transaction_content');
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::dropIfExists('billings');
    }
}