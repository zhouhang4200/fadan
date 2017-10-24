<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('商品名称');
            $table->string('price')->comment('商品单价');
            $table->integer('user_id')->comment('商品所属的用户ID');
            $table->integer('service_id')->comment('服务ID');
            $table->integer('game_id')->comment('游戏ID');
            $table->string('foreign_goods_id')->comment('外部商品号');
            $table->integer('goods_template_id')->comment('商品关联的模版ID');
            $table->tinyInteger('display')->default(0)->comment('是否在前台显示 0 不显示 1 显示');
            $table->timestamps();
            $table->index('goods_template_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goods');
    }
}
