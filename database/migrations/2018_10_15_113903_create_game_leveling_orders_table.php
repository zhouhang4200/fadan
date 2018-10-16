<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameLevelingOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_leveling_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('trade_no', 22)->comment('交易单号');
            $table->integer('status')->unsigned()->default(1)->comment('订单状态');
            $table->string('source_order_no')->comment('淘宝主订单号');
            $table->integer('taobao_status')->unsigned()->default(1)->comment('淘宝订单状态');
            $table->tinyInteger('platform_id')->unsigned()->default(0)->comment('外部接单平台号:1-show91;3-蚂蚁；4-dd373;5-丸子');
            $table->string('platform_no')->default('')->comment('外部接单平台订单号');
            $table->integer('game_id')->unsigned()->comment('游戏ID');
            $table->decimal('amount', 17, 4)->comment('代练价格');
            $table->decimal('source_price', 17, 4)->default(0)->comment('来源价格');
            $table->decimal('security_deposit', 17, 4)->default(0)->comment('安全保证金');
            $table->decimal('efficiency_deposit', 17, 4)->default(0)->comment('效率保证金');
            $table->decimal('poundage', 17, 4)->default(0)->comment('手续费');
            $table->integer('source')->default(1)->comment('订单来源:默认 1-淘宝');
            $table->integer('user_id')->unsigned()->comment('下单用户ID');
            $table->integer('parent_user_id')->unsigned()->comment('下单用户父ID');
            $table->integer('take_user_id')->unsigned()->default(0)->comment('接单用户ID');
            $table->integer('take_parent_user_id')->unsigned()->default(0)->comment('接单用户父ID');
            $table->tinyInteger('top')->default(0)->comment('置顶 0 没有置 1 置顶');
            $table->integer('region_id')->unsigned()->comment('游戏区ID');
            $table->integer('server_id')->unsigned()->comment('游戏服务器ID');
            $table->integer('game_leveling_type_id')->unsigned()->comment('游戏代练类型');
            $table->integer('day')->unsigned()->default(0)->comment('代练天数');
            $table->integer('hour')->unsigned()->default(0)->comment('代练小时');
            $table->string('title', 200)->comment('代练标题');
            $table->string('game_account', 100)->comment('游戏账号');
            $table->string('game_password', 500)->comment('游戏密码');
            $table->string('game_role', 100)->comment('游戏角色');
            $table->string('customer_service_name')->default('')->comment('下单客服');
            $table->string('seller_nick')->default('')->comment('卖家店铺旺旺');
            $table->string('buyer_nick')->default('')->comment('买家旺旺');
            $table->string('pre_sale')->default('')->comment('接单客服');
            $table->string('take_order_password', 30)->default('')->comment('接单密码');
            $table->decimal('price_increase_step', 17, 4)->default(0)->comment('自动加价步长');
            $table->decimal('price_ceiling', 17, 4)->default(0)->comment('自动加价上限');
            $table->dateTime('take_at')->nullable()->comment('接单时间');
            $table->dateTime('top_at')->nullable()->comment('置顶的时间');
            $table->dateTime('apply_complete_at')->nullable()->comment('申请验收时间');
            $table->dateTime('complete_at')->nullable()->comment('订单完成时间');
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
        Schema::dropIfExists('game_leveling_orders');
    }
}
