<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameLevelingChannelRefundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_leveling_channel_refunds', function (Blueprint $table) {
            $table->increments('id');
            $table->string('game_leveling_channel_order_trade_no')->comment('渠道订单号');
            $table->unsignedTinyInteger('game_leveling_type_id')->comment('代练类型ID');
            $table->string('game_leveling_type_name')->comment('代练类型名称');
            $table->unsignedInteger('day')->comment('天');
            $table->unsignedInteger('hour')->comment('小时');
            $table->unsignedTinyInteger('type')->comment('退款类型：1 全额 2 部分');
            $table->unsignedTinyInteger('payment_type')->comment('支付方式:1 支付宝 2 微信');
            $table->unsignedTinyInteger('status')->comment('订单状态:1 待付款 2 进行中 3 待收货 4 完成 6 退款中 7 已退款');
            $table->decimal('amount', 10, 2)->unsigned()->comment('订单金额');
            $table->decimal('payment_amount', 10, 2)->unsigned()->comment('实付金额');
            $table->decimal('refund_amount', 10, 2)->default(0)->unsigned()->comment('申请退款金额');
            $table->string('pic1')->comment('图片1');
            $table->string('pic2')->comment('图片2');
            $table->string('pic3')->comment('图片3');
            $table->string('refund_reason', 150)->comment('申请退款原因');
            $table->string('refuse_refund_reason', 150)->default('')->comment('拒绝退款原因');
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
        Schema::dropIfExists('game_leveling_channel_refunds');
    }
}
