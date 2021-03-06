<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("role_id");
            $table->string("name");
            $table->string("display_name");
            $table->string("desc");
            $table->integer("display_order");
            $table->string("type");
            $table->string("frequency");
            $table->integer("frequency_interval");
            $table->integer("cycles");
            $table->integer("amount");
            $table->string("currency");
            $table->string("remote_id");
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
        Schema::dropIfExists('plans');
    }
}
