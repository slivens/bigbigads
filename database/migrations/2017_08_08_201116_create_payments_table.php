<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->text('details');
			$table->string('number');
			$table->string('description');
			$table->string('client_id');
			$table->string('client_email');
			$table->string('total_amount');
            $table->string('currency_code');
            $table->string('status');
			$table->string('plan');//订阅型的才需要此字段
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
        Schema::dropIfExists('payments');
    }
}
