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
            $table->tinyInteger('platform_id')->unsigned()->default(0)->comment('外部接单平台号:1-show91;3-蚂蚁；4-dd373;5-丸子');
            $table->string('platform_no')->default('')->comment('外部接单平台订单号');
            $table->integer('game_id')->unsigned()->comment('游戏ID');
            $table->string('game_name', 60)->comment('游戏名称');
            $table->decimal('amount', 17, 4)->comment('代练金额');
            $table->decimal('source_price', 17, 4)->comment('原代练金额');
            $table->decimal('security_deposit', 17, 4)->default(0)->comment('安全保证金');
            $table->decimal('efficiency_deposit', 17, 4)->default(0)->comment('效率保证金');
            $table->decimal('poundage', 17, 4)->default(0)->comment('手续费');
            $table->decimal('get_amount', 17, 4)->default(0)->comment('获得金额');
            $table->integer('user_id')->unsigned()->comment('创建订单用户ID');
            $table->string('username', 191)->comment('创建订单用户');
            $table->integer('parent_user_id')->unsigned()->comment('创建订单用户父ID');
            $table->string('parent_username', 191)->comment('创建订单用户父');
            $table->integer('take_user_id')->unsigned()->default(0)->comment('接单用户ID');
            $table->string('take_username', 191)->nullable()->comment('接单用户名');
            $table->integer('take_parent_user_id')->unsigned()->default(0)->comment('接单用户父ID');
            $table->string('take_parent_username', 191)->nullable()->comment('接单用户父用户名');
            $table->boolean('order_type_id')->default(1)->comment('订单类型:1-代练');
            $table->integer('game_type_id')->default(0)->unsigned()->comment('游戏类型ID');
            $table->integer('game_class_id')->default(0)->unsigned()->comment('游戏类别ID');
            $table->integer('region_id')->unsigned()->comment('游戏区ID');
            $table->string('region_name', 60)->comment('游戏区名称');
            $table->integer('server_id')->unsigned()->comment('游戏服务器ID');
            $table->string('server_name', 60)->comment('游戏服务器名称');
            $table->integer('game_leveling_type_id')->unsigned()->comment('游戏代练类型');
            $table->string('game_leveling_type_name', 191)->comment('游戏代练类型名称');
            $table->string('title', 200)->comment('代练标题');
            $table->integer('day')->unsigned()->default(0)->comment('代练天数');
            $table->integer('hour')->unsigned()->default(0)->comment('代练小时');
            $table->string('game_account', 100)->comment('游戏账号');
            $table->string('game_password', 500)->comment('游戏密码');
            $table->string('game_role', 100)->comment('游戏角色');
            $table->string('user_phone', 20)->default('')->comment('发单用户电话');
            $table->string('user_qq', 20)->default('')->comment('发单用户qq');
            $table->string('customer_service_name')->default('')->comment('下单客服');
            $table->string('seller_nick')->default('')->comment('卖家店铺旺旺');
            $table->string('buyer_nick')->default('')->comment('买家旺旺');
            $table->string('pre_sale')->default('')->comment('接单客服');
            $table->text('explain', 65535)->comment('代练说明');
            $table->text('requirement', 65535)->comment('代练要求');
            $table->string('take_order_password', 30)->default('')->comment('接单密码');
            $table->string('player_name', 80)->default('')->comment('玩家名称');
            $table->string('player_phone', 20)->default('0')->comment('玩家电话');
            $table->string('player_qq', 20)->default('0')->comment('玩家QQ');
            $table->dateTime('take_at')->nullable()->comment('接单时间');
            $table->decimal('price_increase_step', 17, 4)->default(0)->comment('自动加价步长');
            $table->decimal('price_ceiling', 17, 4)->default(0)->comment('自动加价上限');
            $table->dateTime('apply_complete_at')->nullable()->comment('申请验收时间');
            $table->dateTime('complete_at')->nullable()->comment('订单完成时间');
            $table->integer('source')->default(1)->comment('订单来源');
            $table->boolean('top')->default(0)->comment('置顶 0 没有置 1 置顶');
            $table->dateTime('top_at')->nullable()->comment('置顶的时间');
            $table->string('parent_user_phone', 60)->nullable()->default('')->comment('创建订单用户父电话');
            $table->string('parent_user_qq', 60)->nullable()->default('')->comment('创建订单用户父QQ');
            $table->string('take_user_qq', 60)->nullable()->default('')->comment('接单用户QQ');
            $table->string('take_user_phone', 60)->nullable()->default('')->comment('接单用户电话');
            $table->string('take_parent_phone', 60)->nullable()->default('')->comment('接单主用户电话');
            $table->string('take_parent_qq', 60)->nullable()->default('')->comment('接单主用户QQ');
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
