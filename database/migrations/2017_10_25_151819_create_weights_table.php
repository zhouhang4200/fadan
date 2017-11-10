<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWeightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weights', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_no', 30)->comment('订单号');
            $table->decimal('order_money', 10, 4)->comment('订单金额');
            $table->integer('creator_user_id')->unsigned()->comment('订单创建者');
            $table->integer('creator_primary_user_id')->unsigned()->comment('订单创建者主账号');
            $table->dateTime('order_time')->comment('订单创建时间');
            $table->integer('gainer_user_id')->unsigned()->comment('接单者');
            $table->integer('gainer_primary_user_id')->unsigned()->comment('接单者主账号');
            $table->dateTime('order_in_time')->comment('订单接入时间');

            $table->dateTime('order_end_time')->nullable()->comment('订单处理结束时间，不代表订单完成了，是否完成要看status字段。这个结束只是说当前用户对该订单的处理结束了');
            $table->integer('order_use_time')->nullable()->comment('订单处理使用时间（秒数，如：3600）');
            $table->tinyInteger('is_produce_after_sale')->default(0)->comment('是否产生售后');
            $table->tinyInteger('is_time_out')->default(0)->comment('订单是否超时');
            $table->tinyInteger('status')->default(0)->comment('订单状态：1.成功 2.失败 3.返回集市');
            $table->string('remark')->nullable()->comment('备注');
            $table->timestamps();

            $table->index('order_no');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('weights');
    }
}
