<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePunishOrRewardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('punish_or_rewards', function (Blueprint $table) {
            $table->increments('id');
            $table->string('no')->comment('订单号');
            $table->string('order_no')->comment('关联订单号');
            $table->integer('user_id')->comment('用户id');
            $table->tinyInteger('type')->comment('类型,1,奖款,2,罚款,3加权重,4，减权重，5禁止接单');
            $table->tinyInteger('status')->default(0)->comment('状态,0，默认状态，1,奖励到账,2,奖励未到账,3未交罚款,4，已交罚款，5已加权重，6已减权重，7,申诉中，8，撤销');
            $table->decimal('sub_money', 12, 4)->nullable()->comment('缴款金额');
            $table->timestamp('deadline')->nullable()->comment('缴款最后期限');
            $table->integer('before_weight_value')->nullable()->comment('用户扣除和增加之前的权重值');
            $table->tinyInteger('ratio')->nullable()->comment('扣除或增加权重的系数，正负数，如-10，即为-10*0.01*权重值');
            $table->integer('after_weight_value')->nullable()->comment('用户扣除和增加之后的权重值');
            $table->timestamp('start_time')->nullable()->comment('生效时间');
            $table->timestamp('end_time')->nullable()->comment('截止时间');
            $table->decimal('add_money', 12, 4)->nullable()->comment('奖励金额');
            $table->string('voucher')->nullable()->comment('凭证照片');
            $table->text('remark')->nullable()->comment('备注');
            $table->tinyInteger('confirm')->default(0)->comment('商户确认');
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
        Schema::dropIfExists('punish_or_rewards');
    }
}
