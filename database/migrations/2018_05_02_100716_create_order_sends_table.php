<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderSendsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_sends', function (Blueprint $table) {
            $table->increments('id');
            $table->string('platform_name');
            $table->tinyInteger('status')->comment('状态 0 失败 1 成功');
            $table->text('send_result')->comment('发送的原始返回');
            $table->text('send_data')->comment('发送的数据');
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
        Schema::dropIfExists('order_sends');
    }
}
