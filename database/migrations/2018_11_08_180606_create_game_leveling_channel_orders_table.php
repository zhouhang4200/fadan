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
            $table->tinyInteger('payment_type')->unsigned()->comment('支付方式:1-支付宝，2-微信');
            $table->unsignedInteger('status')->default(1)->comment('订单状态 1 待付款 2 进行中 3 待收货 4 完成 6 退款中 7 已退款');
            $table->integer('game_id')->unsigned()->comment('游戏ID');
            $table->string('game_name')->comment('游戏名');
            $table->integer('game_region_id')->comment('区id');
            $table->string('game_region_name')->comment('区');
            $table->integer('game_server_id')->comment('服id');
            $table->string('game_server_name')->comment('服');
            $table->integer('game_leveling_type_id')->comment('代练类型id');
            $table->string('game_leveling_type_name')->comment('代练类型');
            $table->string('game_role')->comment('游戏角色名称');
            $table->string('game_account')->comment('游戏账号');
            $table->string('game_password')->comment('游戏密码');
            $table->string('player_phone')->comment('玩家电话');
            $table->string('player_qq')->comment('玩家qq');
            $table->string('user_qq')->comment('商户qq');
            $table->string('title')->comment('订单标题');
            $table->integer('day')->unsigned()->comment('代练天');
            $table->integer('hour')->unsigned()->comment('代练小时');
            $table->string('demand')->comment('代练目标');
            $table->decimal('security_deposit', 10, 2)->unsigned()->comment('安全保证金');
            $table->decimal('efficiency_deposit', 10, 2)->unsigned()->comment('效率保证金');
            $table->string('explain', 1000)->comment('代练说明');
            $table->string('requirement', 1000)->comment('代练要求');
            $table->string('remark', 100)->default('')->comment('备注信息');
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
