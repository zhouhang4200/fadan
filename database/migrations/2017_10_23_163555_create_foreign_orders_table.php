<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForeignOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('foreign_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('channel')->unsigned()->comment('渠道：1，京东；2，天猫；3，卡门');
            $table->string('channel_name')->comment('渠道名字');
            $table->string('kamen_order_no')->comment('卡门订单号');
            $table->string('foreign_order_no')->unique()->comment('外部订单号');
            $table->timestamp('order_time')->comment('订单生成时间');
            $table->string('foreign_goods_id')->comment('外部商品号');
            $table->decimal('single_price', 10, 4)->comment('单价');
            $table->decimal('total_price', 10, 4)->comment('总价');
            $table->string('wang_wang')->nullable()->comment('联系方式:旺旺');
            $table->string('tel')->nullable()->comment('联系方式:手机');
            $table->string('qq')->nullable()->comment('联系方式:qq');
            $table->text('details')->comment('详情');
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
        Schema::dropIfExists('foreign_orders');
    }
}
