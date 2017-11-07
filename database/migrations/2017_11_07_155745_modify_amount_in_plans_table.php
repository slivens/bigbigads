<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyAmountInPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            // 文档中的法子是添加doctrine/dbal依赖，然后用change()
            $table->dropColumn('amount');
        });
        Schema::table('plans', function (Blueprint $table) {
            $table->float('amount')->after('cycles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('amount');
        });
        Schema::table('plans', function (Blueprint $table) {
            $table->integer('amount')->after('cycles');
        });
    }
}
