<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameLevelingOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_leveling_order_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('game_leveling_order_trade_no', 22)->comment('代练订单号');
            $table->string('game_region_name', 60)->comment('游戏区名称');
            $table->string('game_server_name', 60)->comment('游戏服务器名称');
            $table->string('game_leveling_type_name', 191)->comment('游戏代练类型名称');
            $table->string('game_name', 60)->comment('游戏名称');
            $table->string('username', 191)->comment('创建订单用户');
            $table->string('parent_username', 191)->comment('创建订单用户父');
            $table->string('take_username', 191)->nullable()->comment('接单用户名');
            $table->string('take_parent_username', 191)->nullable()->comment('接单用户父用户名');
            $table->string('user_phone', 20)->default('')->comment('发单用户电话');
            $table->string('user_qq', 20)->default('')->comment('发单用户qq');
            $table->string('player_name', 80)->default('')->comment('玩家名称');
            $table->string('player_phone', 20)->default('0')->comment('玩家电话');
            $table->string('player_qq', 20)->default('0')->comment('玩家QQ');
            $table->string('parent_user_phone', 60)->nullable()->default('')->comment('创建订单用户父电话');
            $table->string('parent_user_qq', 60)->nullable()->default('')->comment('创建订单用户父QQ');
            $table->string('take_user_qq', 60)->nullable()->default('')->comment('接单用户QQ');
            $table->string('take_user_phone', 60)->nullable()->default('')->comment('接单用户电话');
            $table->string('take_parent_phone', 60)->nullable()->default('')->comment('接单主用户电话');
            $table->string('take_parent_qq', 60)->nullable()->default('')->comment('接单主用户QQ');
            $table->text('explain')->comment('代练说明');
            $table->text('requirement')->comment('代练要求');
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
        Schema::dropIfExists('game_leveling_order_details');
    }
}
