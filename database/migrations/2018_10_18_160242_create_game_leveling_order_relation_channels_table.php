<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameLevelingOrderRelationChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_leveling_order_relation_channels', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('channel')->comment('渠道类型');
            $table->string('game_leveling_order_trade_no')->comment('订单表订单号');
            $table->string('game_leveling_channel_order_trade_no')->comment('订单号');
            $table->decimal('payment', 10, 4)->default(0)->comment('实付金额');
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
        Schema::dropIfExists('game_leveling_order_relation_channels');
    }
}
