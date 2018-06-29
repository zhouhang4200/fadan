<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMonthSettlementOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('month_settlement_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_no')->comment('订单号');
            $table->string('foreign_order_no')->comment('外部订单号(天猫)');
            $table->integer('creator_primary_user_id')->comment('发单方主ID');
            $table->string('creator_primary_user_name')->comment('发单方名字');
            $table->integer('gainer_primary_user_id')->comment('接单方主ID');
            $table->string('gainer_primary_user_name')->comment('接单方名字');
            $table->decimal('amount', 14, 2)->comment('结算金额');
            $table->integer('game_id')->comment('游戏ID');
            $table->tinyInteger('status')->comment('状态 1 没结算 2 已结算');
            $table->dateTime('finish_time')->comment('订单完结时间');
            $table->dateTime('settlement_time')->nullable()->comment('订单结算时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('month_settlement_orders');
    }
}
