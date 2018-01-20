<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_charges', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_no', 30)->comment('订单号，关联order.no');
            $table->integer('user_id')->comment('用户id');
            $table->string('qs_order_id', 30)->comment('千手订单号');
            $table->unsignedInteger('total_game_gold')->comment('应充游戏币数');
            $table->unsignedInteger('charged_game_gold')->comment('已充游戏币数');
            $table->string('game_gold_unit', 30)->comment('游戏币单位');
            $table->tinyInteger('status')->default(0)->comment('状态：1.充值中 2.充值完成 3.充值未完成');
            $table->string('product_id', 200)->comment('产品id。记录千手数据库的 goods.product_id');
            $table->string('bundle_id', 200)->comment('游戏id。记录千手数据库的 games.bundle_id');
            $table->timestamps();

            $table->unique('order_no');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_charges');
    }
}
