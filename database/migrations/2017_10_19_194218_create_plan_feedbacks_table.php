<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanFeedbacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_feedbacks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('company', 200);
            $table->string('website', 200);
            $table->string('page', 200)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('phone', 64)->nullable();
            $table->string('price')->nullable();
            $table->string('skype', 64)->nullable();
            $table->string('location')->nullable();
            $table->longText('feedback')->nullable();
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
        Schema::dropIfExists('plan_feedbacks');
    }
}
