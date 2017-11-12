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
            $table->tinyInteger('source')->comment('来源渠道: 1.人工下单 2.淘宝 3.天猫 4.京东 具体看配置文件config(order.source)');
            $table->tinyInteger('status')->comment('订单状态：1.已创建 2.停止抢单，分配中 3.已接单 4.已发货 5.发货失败 6.售后中 7.售后完成 具体看配置文件config(order.status)');
            $table->unsignedInteger('goods_id')->comment('商品ID');
            $table->string('goods_name')->comment('商品名称');
            $table->unsignedInteger('service_id')->comment('服务id：services.id');
            $table->string('service_name')->comment('服务名称: services.name');
            $table->unsignedInteger('game_id')->comment('游戏id: games.id');
            $table->string('game_name')->comment('游戏名称：games.name');
            $table->decimal('original_price', 10, 4)->comment('原售价');
            $table->decimal('price', 10, 4)->comment('售价');
            $table->unsignedInteger('quantity')->comment('数量');
            $table->decimal('original_amount', 10, 4)->comment('订单原总额');
            $table->decimal('amount', 10, 4)->comment('订单总额');
            $table->decimal('real_price', 10, 4)->comment('实际订单总额');
            $table->string('remark')->comment('备注说明');
            $table->unsignedInteger('creator_user_id')->comment('订单创建者（主账号或子账号id）');
            $table->unsignedInteger('creator_primary_user_id')->comment('订单创建者主账号id');
            $table->unsignedInteger('gainer_user_id')->default(0)->comment('接单者（主账号或子账号id）');
            $table->unsignedInteger('gainer_primary_user_id')->default(0)->comment('接单者主账号id');
            $table->datetime('created_at');
            $table->datetime('updated_at');
            $table->index('no');
            $table->index('foreign_order_no');
            $table->index('source');
            $table->index('status');
            $table->index('service_id');
            $table->index('game_id');
            $table->index('goods_id');
            $table->index(['creator_user_id', 'status']);
            $table->index(['creator_primary_user_id', 'status']);
            $table->index(['gainer_user_id', 'status']);
            $table->index(['gainer_primary_user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
