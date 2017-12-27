<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderChargeRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_charge_records', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('用户id');
            $table->string('order_no', 30)->comment('订单号');
            $table->unsignedInteger('game_gold')->comment('充的游戏币数');
            $table->tinyInteger('stock_id')->comment('库存id');
            $table->timestamps();

            $table->index('user_id');
            $table->index('order_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_charge_records');
    }
}
