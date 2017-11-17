<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserOrderMoneysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_order_moneys', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comment('用户');
            $table->tinyInteger('source')->unsigned()->comment('来源, 具体看config order source, 所有，0');
            $table->tinyInteger('type')->unsigned()->comment('1,接单，2，发单，3，接单+发单');
            $table->decimal('total', 10, 4)->comment('总金额');
            $table->decimal('waite_user_receive', 10, 4)->comment('等待商家接单总金额');
            $table->decimal('distributing', 10, 4)->comment('等系统分配中总金额');
            $table->decimal('received', 10, 4)->comment('商户已经接单总金额');
            $table->decimal('sended', 10, 4)->comment('商户已经发货总金额');
            $table->decimal('failed', 10, 4)->comment('已经失败总金额');
            $table->decimal('after_saling', 10, 4)->comment('售后中总金额');
            $table->decimal('after_saled', 10, 4)->comment('售后完成总金额');
            $table->decimal('successed', 10, 4)->comment('订单完成总金额');
            $table->decimal('canceled', 10, 4)->comment('订单取消总金额');
            $table->decimal('waite_pay', 10, 4)->comment('未付款总金额');
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
        Schema::dropIfExists('user_order_moneys');
    }
}
