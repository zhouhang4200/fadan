<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameLevelingOrderConsultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_leveling_order_consults', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comment('用户ID');
            $table->integer('parent_user_id')->unsigned()->comment('用户父ID');
            $table->string('game_leveling_order_trade_no', 22)->comment('代练订单交易号');
            $table->decimal('amount', 17)->comment('代练费');
            $table->decimal('security_deposit', 17, 4)->comment('安全保证金');
            $table->decimal('efficiency_deposit', 17, 4)->comment('效率保证金');
            $table->decimal('poundage', 17, 4)->default(0.00)->comment('手续费');
            $table->string('reason', 500)->comment('撤销原因');
            $table->tinyInteger('status')->default(1)->comment('状态 1 处理中 2 成功 3 失败');
            $table->tinyInteger('initiator')->comment('发起人 1 发单方 2 接单方');
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
        Schema::dropIfExists('game_leveling_order_consults');
    }
}
