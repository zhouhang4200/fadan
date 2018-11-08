<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameLevelingChannelDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_leveling_channel_discounts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('game_id')->unsigned()->comment('游戏ID');
            $table->string('game_name')->comment('游戏名称');
            $table->string('game_leveling_type_id')->comment('游戏类型');
            $table->integer('level')->unsigned()->comment('层级数');
            $table->integer('discount')->unsigned()->comment('折扣');
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
        Schema::dropIfExists('game_leveling_channel_discounts');
    }
}
