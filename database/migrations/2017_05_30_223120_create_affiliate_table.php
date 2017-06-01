<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAffiliateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->comment("名称");
            $table->string('email', 100);
            $table->string('password');
            $table->string('telephone', 64)->nullable();
            $table->string('address')->nullable();
            $table->string('track', 64)->unique()->comment('跟踪码');
            $table->integer('status')->default(0)->comment('状态');
            $table->integer('type')->default(0)->comment('类型');
            $table->integer('click')->default(0)->comment('点击数');
            $table->integer('action')->default(0)->comment('行动数');
            $table->float('share')->default(0)->comment('分成');
            $table->float('balance')->default(0)->comment('余额');
            $table->timestamps();
        });

        Schema::create('affiliate_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('track');
            $table->ipAddress('ip');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->integer('affiliate_id')->default(0);
            $table->ipAddress('regip')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('affiliate_id', 'regip');
        });
        Schema::dropIfExists('affiliate_logs');
        Schema::dropIfExists('affiliates');
    }
}
