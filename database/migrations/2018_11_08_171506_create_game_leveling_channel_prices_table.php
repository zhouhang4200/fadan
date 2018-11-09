<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameLevelingChannelPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_leveling_channel_prices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('game_leveling_channel_game_id')->unsigned()->comment('关联渠道开通的游戏');
            $table->integer('sort')->unsigned()->comment('排序');
            $table->string('level')->comment('层级');
            $table->decimal('price', 10, 2)->unsigned()->comment('层级价格');
            $table->integer('hour')->unsigned()->comment('层级时间');
            $table->decimal('security_deposit', 10, 2)->unsigned()->comment('该层级安全保证金');
            $table->decimal('efficiency_deposit', 10, 2)->unsigned()->comment('该层级效率保证金');
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
        Schema::dropIfExists('game_leveling_channel_prices');
    }
}
