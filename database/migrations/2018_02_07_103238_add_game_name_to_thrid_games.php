<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGameNameToThridGames extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('third_games', function (Blueprint $table) {
            $table->string('game_name')->after('game_id')->comment('我们平台游戏名字');
            $table->string('third_game_name')->after('third_game_id')->comment('第三方游戏名字');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('third_games', function (Blueprint $table) {
            $table->dropColumn('game_name');
            $table->dropColumn('third_game_name');
        });
    }
}
