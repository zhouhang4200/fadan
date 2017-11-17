<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_order_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comment('用户');
            $table->tinyInteger('source')->unsigned()->comment('来源, 具体看config order source, 所有，0');
            $table->tinyInteger('type')->unsigned()->comment('1,接单，2，发单，3，接单+发单');
            $table->integer('total')->unsigned()->comment('订单总数');
            $table->integer('waite_user_receive')->unsigned()->comment('等待商家接单数');
            $table->integer('distributing')->unsigned()->comment('等系统分配中数');
            $table->integer('received')->unsigned()->comment('商户已经接单数');
            $table->integer('sended')->unsigned()->comment('商户已经发货数');
            $table->integer('failed')->unsigned()->comment('已经失败数');
            $table->integer('after_saling')->unsigned()->comment('售后中数');
            $table->integer('after_saled')->unsigned()->comment('售后完成数');
            $table->integer('successed')->unsigned()->comment('订单完成数');
            $table->integer('canceled')->unsigned()->comment('订单取消数');
            $table->integer('waite_pay')->unsigned()->comment('未付款数');
            $table->string('most_game_name')->comment('最多的游戏');
            $table->integer('most_game_amount')->unsigned()->comment('最多的游戏数');
            $table->timestamp('time')->comment('日期');
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
        Schema::dropIfExists('user_order_details');
    }
}
