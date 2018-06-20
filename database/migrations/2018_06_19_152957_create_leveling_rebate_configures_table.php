<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLevelingRebateConfiguresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leveling_rebate_configures', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('game_id')->unsigned()->comment('游戏ID');
            $table->string('game_name')->comment('游戏名称');
            $table->string('game_leveling_type')->comment('游戏类型');
            $table->integer('level_count')->unsigned()->comment('层级数');
            $table->integer('rebate')->unsigned()->comment('折扣');
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
        Schema::dropIfExists('leveling_rebate_configures');
    }
}
