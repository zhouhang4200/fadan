<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderRegionCorrespondencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_region_correspondences', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('game_id')->unsigned()->comment('我们平台的游戏ID');
            $table->string('game_name')->comment('我们平台的游戏名字');
            $table->tinyInteger('third')->unsigned()->comment('第三方平台号：1 91代练，2 代练妈妈');
            $table->string('third_game_id')->comment('第三方平台游戏ID');
            $table->string('third_game_name')->comment('第三方平台游戏名字');
            $table->integer('area_id')->unsigned()->comment('我们的区ID');
            $table->string('area_name')->comment('我们区名');
            $table->string('third_area_id')->comment('第三方区ID');
            $table->string('third_area_name')->comment('第三方区名');
            $table->integer('server_id')->unsigned()->comment('我们服ID');
            $table->string('server_name')->comment('我们的服名');
            $table->string('third_server_id')->comment('第三方服ID');
            $table->string('third_server_name')->comment('第三方服名');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_region_correspondences');
    }
}
