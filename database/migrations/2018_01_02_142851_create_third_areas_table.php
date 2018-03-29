<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateThirdAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('third_areas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('game_id')->unsigned()->comment('我们的游戏id');
            $table->tinyInteger('third_id')->unsigned()->comment('第三方平台id，1：91代练');
            $table->integer('area_id')->unsigned()->comment('我们的区id');
            $table->string('third_area_id')->unsigned()->comment('第三方区id');
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
        Schema::dropIfExists('third_areas');
    }
}
