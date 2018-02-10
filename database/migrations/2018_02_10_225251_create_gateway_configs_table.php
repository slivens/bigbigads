<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGatewayConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gateway_configs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('gateway_name', 191)->unique();
            $table->string('factory_name');
            $table->json('config');
            $table->timestamps();
        });
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->integer('gateway_id')->unsigned();
            $table->foreign('gateway_id')->references('id')->on('gateway_configs');
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
            $table->dropForeign(['gateway_id']);
            $table->dropColumn('gateway_id');
        });
        Schema::dropIfExists('gateway_configs');
    }
}
