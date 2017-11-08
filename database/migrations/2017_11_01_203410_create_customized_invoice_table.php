<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomizedInvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customized_invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('company_name', 200)->nullable();
            $table->string('address', 200)->nullable();
            $table->string('contact_info', 200)->nullable();
            $table->string('website', 200)->nullable();
            $table->string('tax_no', 200)->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customized_invoices');
    }
}
