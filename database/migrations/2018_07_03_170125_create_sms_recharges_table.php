<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsRechargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_recharges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_no')->commnet('充值单号');
            $table->integer('user_id')->comment('充值用户');
            $table->integer('before_amount')->comment('充值前余额');
            $table->integer('amount')->comment('充值数量');
            $table->integer('after_amount')->comment('充值后余额');
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
        Schema::dropIfExists('sms_recharges');
    }
}
