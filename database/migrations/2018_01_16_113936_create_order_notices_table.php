<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderNoticesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_notices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('creator_user_id')->unsigned()->comment('发单人id');
            $table->integer('creator_primary_user_id')->unsigned()->comment('发单人主id');
            $table->integer('gainer_user_id')->unsigned()->comment('接单人id');
            $table->string('creator_user_name')->comment('发单人账号');
            $table->string('order_no')->comment('平台订单号');
            $table->string('third_order_no')->comment('第三方订单号');
            $table->tinyInteger('third')->unsigned()->comment('第三方平台：1，91代练， 2，代练妈妈， 3，代练通， 4，易代练');
            $table->tinyInteger('status')->unsigned()->comment('我们平台状态，参考 config.order ');
            $table->tinyinteger('third_status')->unsigned()->comment('外部订单状态， 同样参考 config.order ');
            $table->timestamp('create_order_time')->comment('订单发布时间');
            $table->tinyInteger('complete')->unsigned()->default(0)->comment('是否完成, 默认0， 完成1');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_notices');
    }
}
