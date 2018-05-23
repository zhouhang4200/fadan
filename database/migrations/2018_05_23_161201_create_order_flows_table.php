<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderFlowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_flows', function (Blueprint $table) {
            // $table->increments('id');
            // $table->string('order_no')->comment('订单号');
            // $table->tinyInteger('third')->unsigned()->comment('第三方平台号');
            // $table->string('third_order_no')->comment('第三方订单号');
            // $table->string('tid')->comment('天猫订单号');
            // $table->decimal('price', 10, 2)->comment('发布价格');
            // $table->decimal('original_price', 10, 2)->comment('来源价格');
            // $table->decimal('consult_price', 10, 2)->comment('协商时商家填的愿意付的代练费或打手需要商家支付的代练费');
            // $table->decimal('consult_deposit', 10, 2)->comment('协商时商家需要打手赔付的双金或打手愿意赔偿的双金');
            // $table->decimal('api_price', 10, 2)->comment('第三方平台协商或仲裁商家需要支付的代练费');
            // $table->decimal('api_deposit', 10, 2)->comment('第三方平台协商或仲裁赔偿给商家的双金');
            // $table->decimal('api_service', 10, 2)->comment('第三方平台协商或仲裁需要商家支付的手续费');
            // $table->decimal('order_complain_payment')->comment('订单发生投诉支出金额');
            // $table->decimal('order_complain_income')->comment('订单发生投诉获得赔偿金额');
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_flows');
    }
}
