<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameLevelingOrderLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_leveling_order_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('game_leveling_order_trade_no', 22)->comment('订单号：game_leveling_order.trade_no');
            $table->unsignedInteger('user_id')->default(0)->comment('用户ID');
            $table->string('username')->nullable()->comment('用户名');
            $table->unsignedInteger('parent_user_id')->nullable()->comment('用户父ID');
            $table->unsignedInteger('admin_user_id')->default(0)->comment('管理员id: admin_users.id');
            $table->integer('type')->comment('操作类型');
            $table->string('name', 100)->comment('操作名称');
            $table->string('description', 300)->comment('描述');
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
        Schema::dropIfExists('game_leveling_order_logs');
    }
}
