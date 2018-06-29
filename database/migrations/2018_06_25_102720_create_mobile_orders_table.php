<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMobileOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobile_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('no')->comment('订单号');
            $table->string('order_no')->comment('外部订单号');
            $table->tinyInteger('channel')->comment('渠道:1-咸鱼,2-转转');
            $table->tinyInteger('status')->unsigned()->comment('订单状态:0-未付款，其他与订单表一致');
            $table->integer('game_id')->unsigned()->comment('游戏ID');
            $table->string('game_name')->comment('游戏名');
            $table->string('region')->comment('区');
            $table->string('server')->comment('服');
            $table->string('role')->comment('角色名称');
            $table->string('account')->comment('账号');
            $table->string('password')->comment('密码');
            $table->string('client_phone')->comment('玩家电话');
            $table->string('client_qq')->comment('玩家qq');
            $table->string('user_qq')->comment('商户qq');
            $table->string('demand')->comment('代练目标');
            $table->integer('game_leveling_day')->unsigned()->comment('代练天');
            $table->integer('game_leveling_hour')->unsigned()->comment('代练小时');
            $table->decimal('security_deposit', 10, 4)->unsigned()->comment('安全保证金');
            $table->decimal('efficiency_deposit', 10, 4)->unsigned()->comment('效率保证金');
            $table->string('game_leveling_title')->comment('订单标题');
            $table->string('game_leveling_type')->comment('代练类型');
            $table->tinyInteger('pay_type')->unsigned()->comment('支付方式:1-吃支付宝，2-微信');
            $table->decimal('original_price', 10, 4)->comment('来源价格（玩家支付价格）');
            $table->decimal('price', 10, 4)->comment('代练价格');
            $table->integer('creator_user_id')->comment('订单创建人ID');
            $table->string('creator_username')->comment('订单创建人昵称');
            $table->integer('gainer_user_id')->default(0)->comment('接单人ID');
            $table->string('remark', 100)->default('')->comment('备注信息');
            $table->string('game_leveling_instructions', 500)->default('')->comment('代练说明');
            $table->string('game_leveling_requirements', 500)->default('')->comment('代练要求');
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
        Schema::dropIfExists('mobile_orders');
    }
}
