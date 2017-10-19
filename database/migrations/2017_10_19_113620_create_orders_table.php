<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('number')->comment('订单号');
            $table->tinyInteger('status')->comment('订单状态：1.已创建 2.分配中 3.已接单 4.已发货 5.发货失败 6.申请售后 7.售后完成');
            $table->unsignedInteger('category_id')->comment('类目ID');
            $table->unsignedInteger('category_id_parent')->comment('类目父ID');
            $table->string('category_name')->comment('1级类目名');
            $table->string('category_name_parent')->comment('2级类目名');
            $table->unsignedInteger('goods_id')->comment('商品ID');
            $table->unsignedInteger('goods_name')->comment('商品名称');
            $table->unsignedInteger('goods_original_price')->comment('原售价');
            $table->unsignedInteger('goods_price')->comment('售价');
            $table->unsignedInteger('goods_quantity')->comment('数量');
            $table->unsignedInteger('original_amount')->comment('订单原总额');
            $table->unsignedInteger('amount')->comment('订单总额');
            $table->tinyInteger('source')->comment('来源渠道: 1.人工下单 2.天猫 3.京东');
            $table->unsignedInteger('creator_user_id')->comment('订单创建者（主账号或子账号id）');
            $table->unsignedInteger('creator_primary_user_id')->comment('订单创建者主账号id');
            $table->unsignedInteger('gainer_user_id')->comment('接单者（主账号或子账号id）');
            $table->unsignedInteger('gainer_primary_user_id')->comment('接单者主账号id');
            $table->datetime('created_at');
            $table->datetime('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order');
    }
}
