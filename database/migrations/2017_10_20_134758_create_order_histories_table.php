<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_no')->comment('订单号：orders.no');
            $table->unsignedInteger('user_id')->nullable()->comment('商户id: users.id');
            $table->unsignedInteger('admin_user_id')->nullable()->comment('管理员id: admin_users.id');
            $table->tinyInteger('type')->comment('操作类型');
            $table->string('name')->comment('操作名称');
            $table->string('description')->comment('描述');
            $table->string('before', 2000)->comment('操作前');
            $table->string('after', 2000)->comment('操作后');
            $table->datetime('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_histories');
    }
}
