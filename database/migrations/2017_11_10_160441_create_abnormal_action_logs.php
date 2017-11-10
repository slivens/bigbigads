<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAbnormalActionLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('abnormal_action_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('ip', 80);
            $table->text('remark'); // 错误说明
            $table->text('param');  // 用户请求参数
            $table->longText('exception_message'); // 错误栈，无则不记录
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
        Schema::dropIfExists('abnormal_action_logs');
    }
}
