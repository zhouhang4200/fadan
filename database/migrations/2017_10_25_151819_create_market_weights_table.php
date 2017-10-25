<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMarketWeightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('market_weights', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('creator_user_id')->unsigned()->comment('订单创建者');
            $table->integer('creator_primary_user_id')->unsigned()->comment('订单创建者主键');
            $table->integer('gainer_user_id')->unsigned()->comment('接单者');
            $table->integer('gainer_primary_user_id')->unsigned()->comment('接单者主键');
            $table->integer('order_no')->unsigned()->comment('订单号');
            $table->dateTime('order_time')->comment('订单创建时间');
            $table->dateTime('order_in_time')->comment('订单接入时间');
            $table->dateTime('order_out_time')->comment('订单转出时间');
            $table->dateTime('order_end_time')->comment('订单完成时间');
            $table->dateTime('order_use_time')->comment('订单使用时间');
            $table->decimal('order_money', 10, 4)->comment('订单金额');
            $table->tinyInteger('is_produce_after_sale')->unsigned()->comment('是否产生售后');
            $table->tinyInteger('is_time_out')->unsigned()->comment('订单是否超时');
            $table->tinyInteger('status')->unsigned()->comment('订单是否完成');
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
        Schema::dropIfExists('market_weights');
    }
}
