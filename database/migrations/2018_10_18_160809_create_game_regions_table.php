<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameRegionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_regions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('game_id')->unsigned()->comment('游戏ID');
            $table->string('name', 60)->comment('游戏名称');
            $table->string('initials', 8)->comment('首字母拼音');
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
        Schema::dropIfExists('game_leveling_regions');
    }
}
