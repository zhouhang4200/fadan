<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderSendChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_send_channels', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comment('主账号ID');
            $table->integer('game_id')->unsigned()->comment('代练游戏ID');
            $table->string('game_name')->comment('代练游戏名称');
            $table->string('third')->comment('多个平台组成名称1-2-3-4-5');
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
        Schema::dropIfExists('order_send_channels');
    }
}
