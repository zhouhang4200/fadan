<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThirdGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('third_games', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('third_id')->unsigned()->comment('第三方平台id，1：91代练');
            $table->integer('game_id')->unsigned()->comment('我们的游戏id');
            $table->string('third_game_id')->unsigned()->comment('第三方游戏id');
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
        Schema::dropIfExists('third_games');
    }
}
