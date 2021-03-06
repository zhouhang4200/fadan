<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameLevelingRequirementsTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_leveling_requirements_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->comment('主账号');
            $table->integer('game_id')->default(0)->comment('游戏Id');
            $table->tinyInteger('status')->default(0)->unsigned()->comment('是否为默认,每个账号只能有一个默认：0不默认， 1默认');
            $table->string('name')->comment('模板名称');
            $table->text('content')->comment('模板内容');
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
        Schema::dropIfExists('game_leveling_requirements_templates');
    }
}
