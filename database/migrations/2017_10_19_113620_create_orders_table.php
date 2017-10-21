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
            $table->string('no', 30)->comment('编号');
            $table->string('foreign_order_no')->comment('外部订单号');
            $table->tinyInteger('source')->comment('来源渠道: 1.人工下单 2.天猫 3.京东');
            $table->tinyInteger('status')->comment('订单状态：1.已创建 2.已接单 3.已发货 4.发货失败 5.售后中 6.售后完成');
            $table->unsignedInteger('category_id')->comment('类目ID');
            $table->unsignedInteger('category_id_parent')->comment('父类目ID');
            $table->string('category_name')->comment('类目名');
            $table->string('category_name_parent')->comment('父类目名');
            $table->unsignedInteger('goods_id')->comment('商品ID');
            $table->unsignedInteger('goods_name')->comment('商品名称');
            $table->unsignedInteger('origin_price')->comment('原售价');
            $table->unsignedInteger('price')->comment('售价');
            $table->unsignedInteger('quantity')->comment('数量');
            $table->unsignedInteger('original_amount')->comment('订单原总额');
            $table->unsignedInteger('amount')->comment('订单总额');
            $table->string('remark')->comment('备注说明');
            $table->unsignedInteger('creator_user_id')->comment('订单创建者（主账号或子账号id）');
            $table->unsignedInteger('creator_primary_user_id')->comment('订单创建者主账号id');
            $table->unsignedInteger('gainer_user_id')->nullable()->comment('接单者（主账号或子账号id）');
            $table->unsignedInteger('gainer_primary_user_id')->nullable()->comment('接单者主账号id');
            $table->datetime('created_at');
            $table->datetime('updated_at');
            $table->unique('no');
            $table->index('foreign_order_no');
            $table->index('source');
            $table->index('status');
            $table->index('category_id');
            $table->index('category_id_parent');
            $table->index('goods_id');
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
