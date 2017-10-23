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
            $table->tinyInt('channel')->comment('渠道：1，京东；2，天猫；3，卡门')；
            $table->string('foreign_order_id')->unique()->comment('外部订单号');
            $table->string('foreign_goods_id')->comment('外部商品号');
            $table->decimal('single_price', 17, 4)->comment('单价');
            $table->decimal('total_price', 17, 4)->comment('总价');
            $table->string('contact_way')->comment('联系方式');
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
