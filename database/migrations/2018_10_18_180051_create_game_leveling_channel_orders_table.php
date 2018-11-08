<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameLevelingChannelOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_leveling_channel_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('trade_no')->comment('渠道订单号');
            $table->string('user_id')->default('0')->comment('users表中的用户代表不同渠道');
            $table->string('game_leveling_channel_user_id')->default('0')->comment('渠道的C端用户ID,根据渠道传过来的信息建立的用户');
            $table->decimal('amount', 2)->default(0)->comment('订单金额');
            $table->decimal('discount_amount', 2)->default(0)->comment('优惠金额');
            $table->decimal('payment_amount', 2)->default(0)->comment('实际支付金额');
            $table->decimal('refund_amount', 2)->default(0)->comment('退款金额');
            $table->unsignedInteger('status')->default(1)->comment('订单状态 1 待付款 2 进行中 3 待收货 4 完成 6 退款中 7 已退款');
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
        Schema::dropIfExists('game_leveling_channel_orders');
    }
}
