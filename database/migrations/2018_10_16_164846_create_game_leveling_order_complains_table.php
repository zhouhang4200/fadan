<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameLevelingOrderComplainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_leveling_order_complains', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comment('用户ID');
            $table->integer('parent_user_id')->unsigned()->comment('用户父ID');
            $table->string('game_leveling_order_trade_no', 22)->comment('代练订单交易号');
            $table->decimal('amount', 17)->default(0.00)->comment('代练费');
            $table->decimal('security_deposit', 17)->default(0.00)->comment('安全保证金');
            $table->decimal('efficiency_deposit', 17)->default(0.00)->comment('效率保证金');
            $table->string('reason', 500)->comment('申请仲裁原因');
            $table->string('result', 500)->nullable()->comment('处理结果');
            $table->string('remark', 500)->nullable()->comment('客服备注');
            $table->tinyInteger('status')->default(1)->comment('状态 1 处理中 2 成功 3取消');
            $table->tinyInteger('initiator')->comment('发起人 1 发单方 2 接单方');
            $table->timestamp('dispose_at')->nullable()->comment('处理时间');
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
        Schema::dropIfExists('game_leveling_order_complains');
    }
}
