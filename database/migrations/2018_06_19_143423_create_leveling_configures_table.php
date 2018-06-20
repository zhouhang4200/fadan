<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLevelingConfiguresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leveling_configures', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('game_id')->unsigned()->comment('游戏ID');
            $table->string('game_name')->comment('游戏名称');
            $table->string('game_leveling_type')->comment('游戏类型');
            $table->integer('rebate')->unsigned()->comment('发单价格固定比例折扣');
            $table->string('game_leveling_instructions')->comment('代练说明');
            $table->string('game_leveling_requirements')->comment('代练要求');
            $table->string('user_qq')->comment('商户QQ');
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
        Schema::dropIfExists('leveling_configures');
    }
}
