<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserRechargeOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_recharge_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('no', 30)->comment('编号');
            $table->string('foreign_order_no')->comment('外部单号');
            $table->string('wangwang')->comment('淘宝旺旺id');
            $table->decimal('fee', 10, 4)->comment('金额');
            $table->unsignedInteger('creator_user_id')->comment('创建者（主账号或子账号id）');
            $table->unsignedInteger('creator_primary_user_id')->comment('订单创建者主账号id');
            $table->string('remark', 2000)->comment('备注说明');
            $table->integer('type');
            $table->datetime('created_at');

            $table->unique('no');
            $table->index('foreign_order_no');
            $table->index('creator_user_id');
            $table->index('creator_primary_user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_recharge_orders');
    }
}
