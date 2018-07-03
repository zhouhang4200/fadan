<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderSendResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_send_results', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_no')->comment('内部订单号');
            $table->string('third_name')->comment('平台名字');
            $table->string('third_order_no')->default('')->comment('发布成功后订单号');
            $table->string('status')->comment('状态 1 成功 2 失败');
            $table->string('result')->comment('发送结果');
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
        Schema::dropIfExists('order_send_results');
    }
}
