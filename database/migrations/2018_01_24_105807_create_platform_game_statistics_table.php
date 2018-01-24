<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlatformGameStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('platform_game_statistics', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date')->comment('日期');
            $table->integer('game_id')->unsigned()->comment('游戏id');
            $table->integer('total_order_count')->unsigned()->comment('发布单数');
            $table->double('wang_wang_order_evg', 8, 2)->unsigned()->comment('单旺旺号平均发单');
            $table->string('use_time')->comment('完单耗时时间戳');
            $table->string('use_time_avg')->comment('完单平均耗时时间戳');
            $table->integer('receive_order_count')->unsigned()->comment('被接单数');
            $table->integer('complete_order_count')->unsigned()->comment('已结算单数');
            $table->double('complete_order_rate', 8, 2)->unsigned()->comment('已结算占比');
            $table->decimal('complete_order_amount', 10, 2)->unsigned()->comment('已结算总支付');
            $table->decimal('complete_order_amount_avg', 10, 2)->unsigned()->comment('已结算平均支付');
            $table->integer('revoke_order_count')->unsigend()->comment('已撤销单数');
            $table->double('revoke_order_rate', 8, 2)->unsigend()->comment('已撤销占比');
            $table->integer('arbitrate_order_count')->unsigend()->comment('已仲裁单数');
            $table->double('complain_order_rate', 8, 2)->unsigend()->comment('已仲裁占比');
            $table->integer('done_order_count')->unsigned()->comment('已完结单数');
            $table->decimal('total_security_deposit')->unsigned()->comment('完单安全保证金');
            $table->decimal('security_deposit_avg', 10, 2)->unsigned()->comment('完单平均安全保证金');
            $table->decimal('total_efficiency_deposit', 10, 2)->unsigned()->comment('完单效率效率保证金');
            $table->decimal('efficiency_deposit_avg', 10, 2)->unsigned()->comment('完单平均效率保证金');
            $table->decimal('total_original_amount', 10, 2)->unsigned()->comment('完单总来源价格');
            $table->decimal('original_amount_avg', 10, 2)->unsigned()->comment('完单平均来源价格');
            $table->decimal('total_amount', 10, 2)->unsigned()->comment('完单总发单价格');
            $table->decimal('amount_avg', 10, 2)->unsigned()->comment('完单平均发单价格');
            $table->decimal('total_revoke_payment', 10, 2)->unsigned()->comment('撤销总支付');
            $table->decimal('revoke_payment_avg', 10, 2)->unsigned()->comment('撤销平均支付');
            $table->decimal('total_complain_payment', 10, 2)->unsigned()->comment('仲裁总支付');
            $table->decimal('complain_payment_avg', 10, 2)->unsigned()->comment('仲裁平均支付');
            $table->decimal('total_revoke_income', 10, 2)->unsigned()->comment('撤销总收入');
            $table->decimal('revoke_income_avg', 10, 2)->unsigned()->comment('撤销平均收入');
            $table->decimal('total_complain_income', 10, 2)->unsigned()->comment('仲裁总收入');
            $table->decimal('complain_income_avg', 10, 2)->unsigned()->comment('仲裁平均收入');
            $table->decimal('total_poundage', 10, 2)->unsigned()->comment('总手续费');
            $table->decimal('poundage_avg', 10, 2)->unsigned()->comment('平均手续费');
            $table->decimal('user_total_profit', 10, 2)->unsigned()->comment('商户总利润');
            $table->decimal('user_profit_avg', 10, 2)->unsigned()->comment('商户平均利润');
            $table->decimal('platform_total_profit', 10, 2)->unsigned()->comment('平台总利润');
            $table->decimal('platform_profit_avg', 10, 2)->unsigned()->comment('平台平均利润');
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
        Schema::dropIfExists('platform_game_statistics');
    }
}
