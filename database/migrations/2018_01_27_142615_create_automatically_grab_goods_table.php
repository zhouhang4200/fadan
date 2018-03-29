<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * 抓取商品配置表
 * Class CreateAutomaticallyGrabGoodsTable
 */
class CreateAutomaticallyGrabGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('automatically_grab_goods', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('商户ID');
            $table->integer('service_id')->comment('服务ID');
            $table->string('foreign_goods_id')->comment('外部商品ID');
            $table->tinyInteger('status')->default(1)->comment('状态 1 开启 2 关闭');
            $table->string('remark', 300)->comment('备注');
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
        Schema::dropIfExists('automatically_grab_goods');
    }
}
