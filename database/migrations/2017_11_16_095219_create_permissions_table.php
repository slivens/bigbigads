<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->string('desc')->nullable()->after('table_name');
            $table->integer('order')->default(0)->after('desc')->comment("从小到大排序");
            $table->integer('type')->default(0)->after('order')->comment('0表示管理员权限;1表示普通用户权限');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn(['desc', 'order', 'type']);
        });
    }
}
