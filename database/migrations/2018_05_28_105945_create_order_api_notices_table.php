<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderApiNoticesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_api_notices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_no')->comment('内部订单号');
            $table->string('source_order_no')->comment('淘宝单号');
            $table->tinyInteger('status')->unsigned()->comment('订单内部状态');
            $table->tinyInteger('operate')->unsigned()->comment('商户操作');
            $table->string('third')->comment('接单平台名称');
            $table->string('reason', 500)->comment('失败返回消息');
            $table->timestamp('order_created_at')->comment('订单发布时间');
            $table->string('function_name')->comment('调用的接口名称');
            $table->timestamp('deleted_at')->comment('删除时间');
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
        Schema::dropIfExists('order_api_notices');
    }
}
