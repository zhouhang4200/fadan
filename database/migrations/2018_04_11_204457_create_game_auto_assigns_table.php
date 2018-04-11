<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameAutoAssignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_auto_assigns', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('game_id')->comment('游戏ID');
            $table->integer('creator_primary_user_id')->comment('发单人主ID');
            $table->integer('gainer_primary_user_id')->comment('接单人主ID');
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
        Schema::dropIfExists('game_auto_assigns');
    }
}
