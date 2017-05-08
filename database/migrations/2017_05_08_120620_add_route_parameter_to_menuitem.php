<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRouteParameterToMenuitem extends Migration
{
    /**
     * Run the migrations.
     * Voyager需要这两个参数，但数据库却没填充，应该是BUG，这里自行补充
     *
     * @return void
     */
    public function up()
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->string('route')->nullable();
            $table->string('parameters')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn('route', 'parameters');
        });
    }
}
