<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLevelingPriceConfiguresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leveling_price_configures', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('game_id')->unsigned()->comment('游戏ID');
            $table->string('game_name')->comment('游戏名称');
            $table->string('game_leveling_type')->comment('游戏类型');
            $table->integer('game_leveling_number')->unsigned()->comment('序号');
            $table->string('game_leveling_level')->comment('层级');
            $table->decimal('level_price', 10, 4)->unsigned()->comment('层级价格');
            $table->integer('level_hour')->unsigned()->comment('层级时间');
            $table->decimal('level_security_deposit', 10, 4)->unsigned()->comment('该层级安全保证金');
            $table->decimal('level_efficiency_deposit', 10, 4)->unsigned()->comment('该层级效率保证金');
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
        Schema::dropIfExists('leveling_price_configures');
    }
}
