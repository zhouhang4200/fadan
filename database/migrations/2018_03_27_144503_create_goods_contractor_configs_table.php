<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsContractorConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_contractor_configs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment('集市商户ID');
            $table->integer('km_goods_id')->comment('卡门商品ID');
            $table->integer('created_admin_user_id')->comment('添加数据，管理员ID');
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
        Schema::dropIfExists('goods_contractor_configs');
    }
}
