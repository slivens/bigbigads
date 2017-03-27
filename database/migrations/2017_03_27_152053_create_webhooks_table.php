<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebhooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('webhooks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('webhook_id', 100);
            $table->dateTime('created_at');
            $table->string('resource_type', 100);
            $table->string('event_type', 100);
            $table->string('summary', 100);
            $table->string('plan_price', 100);
            $table->string('payer_email', 100);
            $table->string('resource_desc', 100);
            $table->dateTimeTz('resource_next_paytime');
            $table->dateTimeTz('resource_last_paytime');
            $table->string('resource_id', 100);
            $table->dateTimeTz('resource_create_time');
            $table->string('resource_state', 100);
            $table->string('billing_agreement_id',100);
            $table->string('webhook_status', 100);
            $table->text('webhook_content');
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
        //Schema::table('webhooks', function (Blueprint $table) {
        Schema::dropIfExists('webhooks');
        //});
    }
}
