<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsTemplate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('status')->comment('关联goods_template_widget 中ID');
            $table->unsignedInteger('service_id')->comment('如果组件支持用用户义组件值，则此处为用户ID');
            $table->unsignedInteger('game_id')->comment('组件定义的英文名');
            $table->unsignedInteger('created_admin_user_id')->comment('组件的值');
            $table->unsignedInteger('updated_admin_user_id')->comment('组件的值');
            $table->unsignedInteger('level');
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
        Schema::dropIfExists('goods_templates');
    }
}
