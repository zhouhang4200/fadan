<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameLevelingServersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_leveling_servers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('game_leveling_region_id')->unsigned()->comment('区ID');
            $table->string('name', 60)->comment('服务器名称');
            $table->string('initials', 191)->comment('首字母拼音');
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
        Schema::dropIfExists('game_leveling_servers');
    }
}
