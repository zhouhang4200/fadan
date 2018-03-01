<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlatformStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('platform_statistics', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date')->comment('日期');
            $table->integer('user_id')->unsigned()->comment('账号');
            $table->integer('parent_id')->unsigned()->comment('主账号');
            $table->tinyInteger('third')->unsigned()->comment('第三方平台');
            $table->integer('game_id')->unsigned()->comment('订单游戏id');
            $table->integer('order_count')->unsigned()->comment('发单数');
            $table->integer('client_wang_wang_count')->unsigned()->comment('不为空的旺旺号数量');
            $table->integer('distinct_client_wang_wang_count')->unsigned()->comment('去重的旺旺号数量');
            $table->string('done_order_use_time')->comment('完单总耗时时间戳');
            $table->integer('receive_order_count')->unsigned()->comment('已接单数');
            $table->integer('complete_order_count')->unsigned()->comment('已结算单数');
            $table->decimal('complete_order_amount', 10, 2)->comment('已结算总支付');
            $table->integer('revoke_order_count')->unsigend()->comment('已撤销单数');
            $table->integer('arbitrate_order_count')->unsigend()->comment('已仲裁单数');
            $table->integer('done_order_count')->unsigned()->comment('已完结单数');
            $table->decimal('done_order_security_deposit', 10, 2)->comment('已完单安全保证金金额');
            $table->decimal('done_order_efficiency_deposit', 10, 2)->comment('已完单效率效率保证金金额');
            $table->decimal('done_order_original_amount', 10, 2)->comment('已完单总来源金额');
            $table->decimal('done_order_amount', 10, 2)->comment('已完单总发单金额');
            $table->decimal('revoke_payment', 10, 2)->comment('撤销总支付金额');
            $table->decimal('arbitrate_payment', 10, 2)->comment('仲裁总支付金额');
            $table->decimal('revoke_income', 10, 2)->comment('撤销总收入金额');
            $table->decimal('arbitrate_income', 10, 2)->comment('撤销总收入金额');
            $table->decimal('poundage', 10, 2)->comment('总手续费金额');
            $table->decimal('user_profit', 10, 2)->comment('商户总利润金额');
            $table->decimal('platform_profit', 10, 2)->comment('平台总利润金额');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('platform_statistics');
    }
}
