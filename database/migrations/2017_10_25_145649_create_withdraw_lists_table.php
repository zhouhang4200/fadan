<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWithdrawListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdraw_lists', function (Blueprint $table) {
            $table->increments('id');
            $table->string('no', 30)->comment('编号');
            $table->tinyInteger('status')->comment('状态：1.申请提现 2.提现成功 3.拒绝');
            $table->decimal('fee', 10, 4)->comment('金额');
            $table->unsignedInteger('creator_user_id')->comment('创建者（主账号或子账号id）');
            $table->unsignedInteger('creator_primary_user_id')->comment('订单创建者主账号id');
            $table->string('remark')->comment('备注说明');
            $table->datetime('created_at');
            $table->datetime('updated_at');

            $table->unique('no');
            $table->index('status');
            $table->index('creator_user_id');
            $table->index('creator_primary_user_id');
            $table->index('created_at');
            $table->index('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('withdraw_lists');
    }
}
