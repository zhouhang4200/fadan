<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameLevelingForeignOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_leveling_foreign_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('trade_no', 22)->comment('订单号');
            $table->string('taobao_trade_no')->comment('淘宝订单号');
            $table->decimal('payment', 10, 2)->comment('实付金额');
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
        Schema::dropIfExists('game_leveling_foreign_orders');
    }
}
