<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_statistics', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comment('主账号');
            $table->date('date')->comment('日期');
            $table->integer('send_order_count')->unsigned()->default(0)->comment('发布订单数');
            $table->integer('receive_order_count')->unsigned()->default(0)->comment('被接单数, 除了 接单 已下架 删除的订单和');
            $table->integer('complete_order_count')->unsigned()->default(0)->comment('已结算单数');
            $table->float('complete_order_rate', 4, 2)->unsigned()->default(0)->comment('已结算占比, 已结算/被接单');
            $table->integer('revoke_order_count')->unsigned()->default(0)->comment('已撤销单数');
            $table->integer('arbitrate_order_count')->unsigned()->default(0)->comment('已仲裁单数');
            $table->decimal('three_status_original_amount', 10, 4)->unsigned()->default(0)->comment('已结算/撤销/仲裁来源价格和');
            $table->decimal('complete_order_amount', 10, 4)->unsigned()->default(0)->comment('已结算单发单金额');
            $table->decimal('two_status_payment', 10, 4)->unsigned()->default(0)->comment('撤销/仲裁支付金额');
            $table->decimal('two_status_income', 10, 4)->unsigned()->default(0)->comment('撤销/仲裁获得赔偿');
            $table->decimal('poundage', 10, 4)->unsigned()->default(0)->comment('手续费');
            $table->decimal('profit', 10, 4)->unsigned()->default(0)->comment('利润');
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
        Schema::dropIfExists('order_statistics');
    }
}
