<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePunishOrRewardRevisions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('punish_or_reward_revisions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('punish_or_reward_id')->unsigned()->comment('对应表的id');
            $table->string('no')->comment('订单号');
            $table->string('order_no')->comment('关联订单号');
            $table->string('admin_user_name')->comment('操作人');
            $table->string('operate_style')->comment('操作方式');
            $table->string('before_value')->comment('更新前的值');
            $table->string('after_value')->comment('更新后的值');
            $table->string('detail')->comment('详细');
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
        Schema::dropIfExists('punish_or_reward_revisions');
    }
}
