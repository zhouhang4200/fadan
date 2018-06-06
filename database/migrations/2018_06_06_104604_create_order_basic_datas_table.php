<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderBasicDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_basic_datas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_no')->comment('订单号');
            $table->tinyInteger('status')->comment('订单状态');
            $table->tinyInteger('tm_status')->comment('天猫订单状态');
            $table->string('client_wang_wang')->comment('玩家旺旺');
            $table->string('customer_service_name')->comment('下单客服昵称');
            $table->integer('game_id')->comment('游戏id');
            $table->string('game_name')->comment('游戏名称');
            $table->integer('revoke_creator')->comment('撤销发起人');
            $table->integer('arbitration_creator')->comment('仲裁发起人');
            $table->integer('creator_user_id')->unsigned()->comment('发单人id');
            $table->integer('creator_primary_user_id')->unsigned()->comment('发单人主id');
            $table->integer('gainer_user_id')->unsigned()->comment('接单人id');
            $table->integer('gainer_primary_user_id')->unsigned()->comment('接单人主id');
            $table->decimal('price', 10, 4)->comment('发单金额');
            $table->decimal('tm_income', 10, 4)->comment('天猫退款金额');
            $table->decimal('security_deposit', 10, 4)->comment('安全保证金');
            $table->decimal('efficiency_deposit', 10, 4)->comment('效率保证金');
            $table->decimal('original_price', 10, 4)->comment('来源价格');
            $table->decimal('consult_amount', 10, 4)->comment('撤销/仲裁发单方支出代练费');
            $table->decimal('consult_deposit', 10, 4)->comment('撤销/仲裁发单方获得的双金');
            $table->decimal('consult_poundage', 10, 4)->comment('撤销/仲裁发单方支出的手续费');
            $table->decimal('creator_judge_income', 10, 4)->comment('订单产生纠纷裁决发单获得金额');
            $table->decimal('creator_judge_payment', 10, 4)->comment('订单产生纠纷裁决发单支出金额');
            $table->timestamp('order_created_at')->comment('订单发单时间');
            $table->timestamp('order_finished_at')->comment('订单结算时间');
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
        Schema::dropIfExists('order_basic_datas');
    }
}
