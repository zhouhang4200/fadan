<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameLevelingPlatformsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_leveling_platforms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('trade_no', 22)->comment('订单号');
            $table->string('platform_no')->comment('外部平台订单号');
            $table->tinyInteger('platform_id')->unsigned()->comment('外部平台号：1-show91;3-蚂蚁；4-dd373;5-丸子');
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
        Schema::dropIfExists('game_leveling_platforms');
    }
}
