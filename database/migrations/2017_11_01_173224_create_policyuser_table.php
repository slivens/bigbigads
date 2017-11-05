<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePolicyuserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('policies', function (Blueprint $table) {
            $table->increments('id')->unsigned()->change();
        });
        Schema::create('policy_user', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('policy_id')->unsigned(); // 使用了policy_id，polices表会删除再重建时，会自己删除关联关系，所以需要重新设置User的policy，如果使用key则无法建立外键，同时对性能会有影响
            $table->string('value', 128)->nullable();
            /* $table->timestamps(); */

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('policy_id')->references('id')->on('policies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('policy_user');
    }
}
