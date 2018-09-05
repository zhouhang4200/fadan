<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThirdServersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('third_servers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('game_id')->comment('我们的游戏id');
            $table->unsignedTinyInteger('third_id')->comment('第三方平台id，1：91代练');
            $table->unsignedInteger('server_id')->comment('我们的服务器id');
            $table->string('third_server_id')->comment('第三方服务器id');
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
        Schema::dropIfExists('third_servers');
    }
}
